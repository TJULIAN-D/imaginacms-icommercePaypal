<?php

namespace Modules\Icommercepaypal\Http\Controllers\Api;

// Requests & Response
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Base Api
use Modules\Icommerce\Http\Controllers\Api\OrderApiController;
use Modules\Icommerce\Http\Controllers\Api\TransactionApiController;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

// Repositories
use Modules\Icommercepaypal\Repositories\IcommercePaypalRepository;

use Modules\Icommerce\Repositories\PaymentMethodRepository;
use Modules\Icommerce\Repositories\TransactionRepository;
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerce\Repositories\CurrencyRepository;


// Entities
use Modules\Icommercepaypal\Entities\Paypal;

class IcommercePaypalApiController extends BaseApiController
{

    private $icommercepaypal;
    private $paymentMethod;
    private $order;
    private $orderController;
    private $transaction;
    private $transactionController;
    private $currency;

    private $paypal;
   
    public function __construct(

        IcommercePaypalRepository $icommercepaypal,
        PaymentMethodRepository $paymentMethod,
        OrderRepository $order,
        OrderApiController $orderController,
        TransactionRepository $transaction,
        TransactionApiController $transactionController,
        CurrencyRepository $currency
         
    ){
        $this->icommercepaypal = $icommercepaypal;
        $this->paymentMethod = $paymentMethod;
        $this->order = $order;
        $this->orderController = $orderController;
        $this->transaction = $transaction;
        $this->transactionController = $transactionController;
        $this->currency = $currency;
       
    }
    
    /**
     * Init data
     * @param Requests request
     * @param Requests orderID
     * @return route
     */
    public function init(Request $request){

        try {

            $orderID = $request->orderID;
            
            \Log::info('Module Icommercepaypal: Init-ID:'.$orderID);

            $paymentName = config('asgard.icommercepaypal.config.paymentName');

            // Configuration
            $attribute = array('name' => $paymentName);
            $paymentMethod = $this->paymentMethod->findByAttributes($attribute);

            // Order
            $order = $this->order->find($orderID);
            $statusOrder = 1; // Processing

            $productFinal = array(
                'name' => "Name: {$order->first_name} {$order->last_name}",
                'title' => "Order: {$orderID} - {$order->email}"
            );

            // Create Transaction
            $transaction = $this->validateResponseApi(
                $this->transactionController->create(new Request([
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentMethod->id,
                    'amount' => $order->total,
                    'status' => $statusOrder
                ]))
            );
            
            // OrderID Method
            $orderID = $order->id."-".$transaction->id;

            // get currency active
            $currency = $this->currency->getActive();
            $paymentMethod->currency = $currency->code;
            
            // Paypal generate
            $this->paypal = new Paypal($paymentMethod);

            $payment = $this->paypal->generate($productFinal, $order->total, $orderID);
            $redirectRoute = $payment->getApprovalLink();

            
            // Update Transaction External Status
            $external_status = explode('token=',$redirectRoute);
           
            $transactionUp = $this->validateResponseApi(
                $this->transactionController->update($transaction->id,new Request([
                    'external_status' => $external_status[1]
                ]))
            );

            // Response
            $response = [ 'data' => [
                "redirectRoute" => $redirectRoute,
                "external" => true
            ]];
            
            
          } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
              'errors' => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);

    }
    
    /**
     * Response Api Method
     * @param Requests request
     * @return route 
     */
    public function response(Request $request){

        try {
            
            \Log::info('Module Icommercepaypal: Response');

            // Configuration
            $paymentName = config('asgard.icommercepaypal.config.paymentName');
            $attribute = array('name' => $paymentName);
            $paymentMethod = $this->paymentMethod->findByAttributes($attribute);
            
            $this->paypal = new Paypal($paymentMethod);

            // Check the response
            $response = $this->paypal->execute($request->paymentId, $request->PayerID);
            $orderMethod = explode('-',$response->transactions[0]->invoice_number);

            $orderID = $orderMethod[0];
            $transactionID = $orderMethod[1];

            if ($response->state == "approved") {
    
                $newstatusOrder = 13; // Status Order Proccesed
                $external_status = $response->state;

            }else{

                $newstatusOrder = 7;  // Status Order Failed
                $external_status = $response->state;

            }
            
            // Order
            $order = $this->order->find($orderID);

            // Update Transaction
            $transaction = $this->validateResponseApi(
                $this->transactionController->update($transactionID,new Request([
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentMethod->id,
                    'amount' => $order->total,
                    'status' => $newstatusOrder,
                    'external_status' => $external_status
                ]))
            );

            // Update Order Process 
            $orderUP = $this->validateResponseApi(
                $this->orderController->update($order->id,new Request([
                    'order_id' => $order->id,
                    'status_id' => $newstatusOrder,
                ]))
            );

            // Check order
            if (!empty($order))
                $redirectRoute = route('icommerce.order.showorder', [$order->id, $order->key]);
            else
                $redirectRoute = route('homepage');

            // Response
            $response = [ 'data' => [
                "redirectRoute" => $redirectRoute
            ]];

        } catch (\Exception $e) {

            // Search transaction
            $attribute = array('external_status' => $request->token);
            $transaction = $this->transaction->findByAttributes($attribute);

            if(!empty($transaction)){

                $newstatusOrder = 3; // Canceled

                // Update Transaction
                $transactionUP = $this->validateResponseApi(
                    $this->transactionController->update($transaction->id,new Request([
                        'status' => $newstatusOrder,
                        'external_status' => "canceled",
                        'external_code' => $e->getCode()
                    ]))
                );

                // Update Order Process 
                $orderUP = $this->validateResponseApi(
                    $this->orderController->update($transactionUP->order_id,new Request([
                        'status_id' => $newstatusOrder,
                    ]))
                );
            }
            
            //Message Error
            $status = 500;

            $response = [
              'errors' => $e->getMessage(),
              'code' => $e->getCode()
            ];

            //Log Error
            \Log::error('Module Icommercepaypal: Message: '.$e->getMessage());
            \Log::error('Module Icommercepaypal: Code: '.$e->getCode());
            //\Log::error('Module Icommercepaypal: Data: '.$e->getData());

        }

        return response()->json($response, $status ?? 200);

    }

}