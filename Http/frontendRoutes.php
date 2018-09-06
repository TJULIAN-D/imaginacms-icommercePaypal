<?php

use Illuminate\Routing\Router;

    $router->group(['prefix'=>'icommercepaypal'],function (Router $router){
        $locale = LaravelLocalization::setLocale() ?: App::getLocale();

        $router->get('/', [
            'as' => 'icommercepaypal',
            'uses' => 'PublicController@index',
        ]);
        $router->get('/ok', [
            'as' => 'icommercepaypal.ok',
            'uses' => 'PublicController@store',
        ]);
        $router->get('/ko', [
            'as' => 'icommercepaypal.ko',
            'uses' => 'PublicController@ko',
        ]);

    });