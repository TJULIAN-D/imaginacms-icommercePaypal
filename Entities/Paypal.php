<?php

namespace Modules\Icommercepaypal\Entities;

use Illuminate\Database\Eloquent\Model;
use Paypalpayment;

class Paypal
{
	private $_apiContext;
	private $payment;
	private $config;
	private $url_ok;
	private $url_ko;

	public function __construct($config)
	{
		$this->config = $config;
		$this->url_ok = "icommercepaypal.ok";
		$this->url_ko = "icommercepaypal.ko";

		$this->_apiContext = Paypalpayment::ApiContext($this->config->clientid, $this->config->clientsecret);

        $conf = config('paypal_payment');
        $flatConfig = array_dot($conf);
        $flatConfig['EndPoint'] = $this->config->endpoint;
        $flatConfig['mode'] = $this->config->mode;
       
        $this->_apiContext->setConfig($flatConfig);
       
	}
	
	public function generate($product,$amount,$orderId)
	{
		

		$payment = \PaypalPayment::payment()->setIntent('sale')
					->setPayer($this->payer())
					->setTransactions([$this->transaction($product,$amount,$orderId)])
					->setRedirectUrls($this->redirectURLs());
		try {
			$payment->create($this->_apiContext);	
		} catch (Exception $e) {
			dd($e);
		}
		return $payment;
	}

	public function payer()
	{
		return \PaypalPayment::payer()
				->setPaymentMethod('paypal');
	}

	public function transaction($product,$amount,$orderId)
	{
		return \PaypalPayment::transaction()
				->setAmount($this->amount($amount))
				->setItemList($this->items($product,$amount))
				->setDescription('Pago')
				->setInvoiceNumber($orderId);
	}

	
	public function items($product,$amount)
	{
		
		$items = [];
			array_push($items,\PaypalPayment::item()
                ->setName($product["name"])
                ->setDescription($product["title"])
                ->setCurrency($this->config->currency)
                ->setQuantity(1)
                ->setPrice($amount));
		return \PaypalPayment::itemList()->setItems($items);
	}

	
	public function amount($amount)
	{
		return \PaypalPayment::amount()
				->setCurrency($this->config->currency)
				->setTotal($amount);
	}

	
	public function redirectURLs()
	{
		
		return \PaypalPayment::redirectUrls()
				->setReturnUrl(route($this->url_ok))
				->setCancelUrl(route($this->url_ko));
	}

	public function execute($paymentId,$payerId)
	{
		$payment = \PaypalPayment::getById($paymentId,$this->_apiContext);
		$execution = \PaypalPayment::PaymentExecution()
					->setPayerId($payerId);

		return $payment->execute($execution,$this->_apiContext);
	}
	
}