<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/icommercepaypal'], function (Router $router) {
    $router->bind('paypalconfig', function ($id) {
        return app('Modules\Icommercepaypal\Repositories\PaypalconfigRepository')->find($id);
    });
    $router->get('paypalconfigs', [
        'as' => 'admin.icommercepaypal.paypalconfig.index',
        'uses' => 'PaypalconfigController@index',
        'middleware' => 'can:icommercepaypal.paypalconfigs.index'
    ]);
    $router->get('paypalconfigs/create', [
        'as' => 'admin.icommercepaypal.paypalconfig.create',
        'uses' => 'PaypalconfigController@create',
        'middleware' => 'can:icommercepaypal.paypalconfigs.create'
    ]);
    $router->post('paypalconfigs', [
        'as' => 'admin.icommercepaypal.paypalconfig.store',
        'uses' => 'PaypalconfigController@store',
        'middleware' => 'can:icommercepaypal.paypalconfigs.create'
    ]);
    $router->get('paypalconfigs/{paypalconfig}/edit', [
        'as' => 'admin.icommercepaypal.paypalconfig.edit',
        'uses' => 'PaypalconfigController@edit',
        'middleware' => 'can:icommercepaypal.paypalconfigs.edit'
    ]);
    $router->put('paypalconfigs', [
        'as' => 'admin.icommercepaypal.paypalconfig.update',
        'uses' => 'PaypalconfigController@update',
        'middleware' => 'can:icommercepaypal.paypalconfigs.edit'
    ]);
    $router->delete('paypalconfigs/{paypalconfig}', [
        'as' => 'admin.icommercepaypal.paypalconfig.destroy',
        'uses' => 'PaypalconfigController@destroy',
        'middleware' => 'can:icommercepaypal.paypalconfigs.destroy'
    ]);
// append

});
