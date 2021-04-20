<?php

/**
* Get Order Reference Commerce
* @param $order
* @param $transaction
* @return reference
*/
if (!function_exists('icommercepaypal_getOrderRefCommerce')) {

 	
    function icommercepaypal_getOrderRefCommerce($order,$transaction){


        $reference = $order->id."-".$transaction->id;

        return $reference;
    }

}

/**
* Get Payment Method Configuration
* @return collection
*/

if (!function_exists('icommercepaypal_getPaymentMethodConfiguration')) {

 	function icommercepaypal_getPaymentMethodConfiguration(){

        $paymentName = config('asgard.icommercepaypal.config.paymentName');
        $attribute = array('name' => $paymentName);
        $paymentMethod = app("Modules\Icommerce\Repositories\PaymentMethodRepository")->findByAttributes($attribute); 
        
        return $paymentMethod;
    }

}