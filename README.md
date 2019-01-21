# asgardcms-icommercepaypal

## Seeder

    run php artisan module:seed Icommercepaypal

## Vendors
    
    add composer.json 
        "anouar/paypalpayment":"^2.1"

    add the service provider to config/app.php
        'providers' => array(
            // ...
            Anouar\Paypalpayment\PaypalpaymentServiceProvider::class,
        )

    Then add an alias under aliases array.
        'aliases' => array(
            // ...
            'Paypalpayment'   => Anouar\Paypalpayment\Facades\PaypalPayment::class,
        )

    Finally Pulish the package configuration by running this CMD
        php artisan vendor:publish --provider="Anouar\Paypalpayment\PaypalpaymentServiceProvider"


# Configurations

    - Client ID
    - Client Secret
    - Mode (Sandbox or Live)

## API

### Init (Parameters = orderID)
    
    https://icommerce.imagina.com.co/api/icommercepaypal




