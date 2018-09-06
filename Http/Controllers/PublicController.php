<?php

namespace Modules\Icommercepaypal\Http\Controllers;

use Mockery\CountValidator\Exception;

use Modules\Icommercepaypal\Entities\Paypal;
use Modules\Icommercepaypal\Entities\Paypalconfig;

use Modules\Core\Http\Controllers\BasePublicController;
use Route;
use Log;
use Session;

use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserRepository;
use Modules\Icommerce\Repositories\CurrencyRepository;
use Modules\Icommerce\Repositories\ProductRepository;
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerce\Repositories\Order_ProductRepository;
use Modules\Setting\Contracts\Setting;
use Illuminate\Http\Request as Requests;


class PublicController extends BasePublicController
{
  
  private $_apiContext;
  
  private $order;
  private $paypal;
  
  private $user;
  protected $auth;
  
  private $currency;
  private $setting;
  
  public function __construct(Authentication $auth, UserRepository $user, CurrencyRepository $currency, Setting $setting, OrderRepository $order)
  {
    parent::__construct();
    
    $this->auth = $auth;
    $this->user = $user;
    $this->currency = $currency;
    $this->setting = $setting;
    $this->order = $order;
  }
  
  
  public function index(Requests $request)
  {
    
    if ($request->session()->exists('orderID')) {
      
      $orderID = session('orderID');
      $order = $this->order->find($orderID);
      
      $productFinal = array(
        'name' => "Name: {$order->firstname} {$order->lastname}",
        'title' => "Order: {$orderID} - {$order->email}"
      );
      
      //$config = Paypalconfig::first();
      $config = new Paypalconfig();
      $config = $config->getData();
      
      //$currency = $this->currency->getActive();
      
      $this->paypal = new Paypal($config);
      $payment = $this->paypal->generate($productFinal, $order->total, $orderID);
      
      return redirect($payment->getApprovalLink());
      
      
    } else {
      return redirect()->route('homepage');
    }
    //================================== Datos de Prueba
    /*
    $orderID = rand(1, 999999);
    $product = "Producto 1";
    $amount = rand(1, 5);
    */
  }
  
  public function store(Requests $request)
  {
    
    $email_from = $this->setting->get('icommerce::from-email');
    $email_to = explode(',', $this->setting->get('icommerce::form-emails'));
    $sender = $this->setting->get('core::site-name');
    
    $config = new Paypalconfig();
    $config = $config->getData();
    
    $orderID = session('orderID');
    $order = $this->order->find($orderID);
    
    $products = [];
    foreach ($order->products as $product) {
      array_push($products, [
        "title" => $product->title,
        "sku" => $product->sku,
        "quantity" => $product->pivot->quantity,
        "price" => $product->pivot->price,
        "total" => $product->pivot->total,
      ]);
    }
    
    $userEmail = $order->email;
    $userFirstname = "{$order->first_name} {$order->last_name}";
    
    try {
      
      $this->paypal = new Paypal($config);
      
      $response = $this->paypal->execute($request->paymentId, $request->PayerID);
      
      if ($response->state == "approved") {
        
        $success_process = icommerce_executePostOrder($response->transactions[0]->invoice_number, 1, $request);
        $order = $this->order->find($orderID);
        
        $content = [
          'order' => $order,
          'products' => $products,
          'user' => $userFirstname
        ];
        
        $msjTheme = "icommerce::email.success_order";
        $msjSubject = trans('icommerce::common.emailSubject.complete').$order->id;
        $msjIntro = trans('icommerce::common.emailIntro.complete');
        
      } else {
        
        $success_process = icommerce_executePostOrder($response->transactions[0]->invoice_number, 4, $request);
        $order = $this->order->find($orderID);
        $content = [
          'order' => $order,
          'products' => $products,
          'user' => $userFirstname
        ];
        
        $msjTheme = "icommerce::email.error_order";
        $msjSubject = trans('icommerce::common.emailSubject.failed').$order->id;
        $msjIntro = trans('icommerce::common.emailIntro.failed');
        
      }
      
      $mailUser = icommerce_emailSend(['email_from' => [$email_from], 'theme' => $msjTheme, 'email_to' => $userEmail, 'subject' => $msjSubject, 'sender' => $sender, 'data' => array('title' => $msjSubject, 'intro' => $msjIntro, 'content' => $content)]);
      
      $mailAdmin = icommerce_emailSend(['email_from' => [$email_from], 'theme' => $msjTheme, 'email_to' => $email_to, 'subject' => $msjSubject, 'sender' => $sender, 'data' => array('title' => $msjSubject, 'intro' => $msjIntro, 'content' => $content)]);
      
      
    } catch (\PPConnectionException $ex) {
      \Log::info($ex->getMessage());
      /*
          dd($ex->getMessage());
          return response()->json(["error" => $ex->getMessage()], 400);
      */
      
      $success_process = icommerce_executePostOrder($response->transactions[0]->invoice_number, 4, $request);
      $order = $this->order->find($orderID);
      $content = [
        'order' => $order,
        'products' => $products,
        'user' => $userFirstname
      ];
      
      $msjTheme = "icommerce::email.error_order";
      $msjSubject = trans('icommerce::common.emailSubject.failed').$order->id;
      $msjIntro = trans('icommerce::common.emailIntro.failed');
      
      $mailUser = icommerce_emailSend(['email_from' => [$email_from], 'theme' => $msjTheme, 'email_to' => $userEmail, 'subject' => $msjSubject, 'sender' => $sender, 'data' => array('title' => $msjSubject, 'intro' => $msjIntro, 'content' => $content)]);
      
      $mailAdmin = icommerce_emailSend(['email_from' => [$email_from], 'theme' => $msjTheme, 'email_to' => $email_to, 'subject' => $msjSubject, 'sender' => $sender, 'data' => array('title' => $msjSubject, 'intro' => $msjIntro, 'content' => $content)]);
      
    }
    $user = $this->auth->user();
    if (isset($user) && !empty($user))
      if (!empty($order))
        return redirect()->route('icommerce.orders.show', [$order->id]);
      else
        return redirect()->route('homepage')
          ->withSuccess(trans('icommerce::common.order_success'));
    else
      if (!empty($order))
        return redirect()->route('icommerce.order.showorder', [$order->id, $order->key]);
      else
        return redirect()->route('homepage')
          ->withSuccess(trans('icommerce::common.order_success'));
    
  }
  
  public function ko(Requests $request)
  {
    
    $email_from = $this->setting->get('icommerce::from-email');
    $email_to = explode(',', $this->setting->get('icommerce::form-emails'));
    $sender = $this->setting->get('core::site-name');
    
    
    if ($request->session()->exists('orderID')) {
      $orderID = session('orderID');
      $products = [];
      $order = $this->order->find($orderID);
      foreach ($order->products as $product) {
        array_push($products, [
          "title" => $product->title,
          "sku" => $product->sku,
          "quantity" => $product->pivot->quantity,
          "price" => $product->pivot->price,
          "total" => $product->pivot->total,
        ]);
      }
      $userEmail = $order->email;
      $userFirstname = "{$order->first_name} {$order->last_name}";
      
      $success_process = icommerce_executePostOrder($orderID, 4, $request);
      $order = $this->order->find($orderID);
      $content = [
        'order' => $order,
        'products' => $products,
        'user' => $userFirstname
      ];
      
      $msjTheme = "icommerce::email.error_order";
      $msjSubject = trans('icommerce::common.emailSubject.failed').$order->id;
      $msjIntro = trans('icommerce::common.emailIntro.failed');
      
      $mail = icommerce_emailSend(['email_from' => [$email_from], 'theme' => $msjTheme, 'email_to' => $userEmail, 'subject' => $msjSubject, 'sender' => $sender, 'data' => array('title' => $msjSubject, 'intro' => $msjIntro, 'content' => $content)]);
      
    }
    
    $user = $this->auth->user();
    if (isset($user) && !empty($user))
      if (!empty($order))
        return redirect()->route('icommerce.orders.show', [$order->id]);
      else
        return redirect()->route('homepage')
          ->withError(trans('icommerce::common.order_error'));
    else
      if (!empty($order))
        return redirect()->route('icommerce.order.showorder', [$order->id, $order->key]);
      else
        return redirect()->route('homepage')
          ->withError(trans('icommerce::common.order_error'));
    
  }
  
  
}