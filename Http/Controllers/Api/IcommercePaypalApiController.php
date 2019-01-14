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

use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserRepository;

use Modules\Icommerce\Events\OrderWasCreated;

// Entities
use Modules\Icommercepaypal\Entities\Paypal;

class IcommercePaypalApiController extends BaseApiController
{

    private $paypal;
    private $paymentMethod;
    private $order;
    private $orderController;
    private $transactionController;
    private $currency;
    private $user;
    protected $auth;

    public function __construct(
        PaymentMethodRepository $paymentMethod,
        OrderRepository $order,
        OrderApiController $orderController,
        TransactionApiController $transactionController,
        CurrencyRepository $currency,
        Authentication $auth, 
        UserRepository $user
    ){

        $this->paymentMethod = $paymentMethod;
        $this->order = $order;
        $this->orderController = $orderController;
        $this->transactionController = $transactionController;
        $this->currency = $currency;
        $this->auth = $auth;
        $this->user = $user;
    }
    
    /**
     * Init data
     * @param Requests request
     * @param Requests orderid
     * @return route
     */
    public function init(Request $request){

        try {

            $orderID = $request->orderid;
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

            // Response
            $response = [ 'data' => [
                "redirectRoute" => $redirectRoute
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