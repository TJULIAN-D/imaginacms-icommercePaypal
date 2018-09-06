<?php

use Modules\Icommercepaypal\Entities\Paypalconfig;

if (! function_exists('icommercepaypal_get_configuration')) {

    function icommercepaypal_get_configuration()
    {

    	$configuration = new Paypalconfig();
    	//dd($configuration->getData());
    	return $configuration->getData();

    }

}

if (! function_exists('icommercepaypal_get_entity')) {

	function icommercepaypal_get_entity()
    {
    	$entity = new Paypalconfig;
    	return $entity;	
    }

}