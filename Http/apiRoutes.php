<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'icommercepaypal'], function (Router $router) {
    
    $router->get('/{orderid}', [
        'as' => 'icommercepaypal.api.paypal.init',
        'uses' => 'IcommercePaypalApiController@init',
    ]);

    $router->get('/method/response', [
        'as' => 'icommercepaypal.api.paypal.response',
        'uses' => 'IcommercePaypalApiController@response',
    ]);

});