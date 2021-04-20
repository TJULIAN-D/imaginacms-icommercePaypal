<?php

return [
  'formFields' => [
        'title' => [
            'value' => null,
            'name' => 'title',
            'type' => 'input',
            'isTranslatable' => true,
            'props' => [
                'label' => 'icommerce::common.title'
            ]
        ],
        'description' => [
            'value' => null,
            'name' => 'description',
            'type' => 'input',
            'isTranslatable' => true,
            'props' => [
                'label' => 'icommerce::common.description',
                'type' => 'textarea',
                'rows' => 3,
            ]
        ],
        'status' => [
            'value' => 0,
            'name' => 'status',
            'type' => 'select',
            'props' => [
              'label' => 'icommerce::common.status',
              'useInput' => false,
              'useChips' => false,
              'multiple' => false,
              'hideDropdownIcon' => true,
              'newValueMode' => 'add-unique',
              'options' => [
                ['label' => 'Activo','value' => 1],
                ['label' => 'Inactivo','value' => 0],
              ]
            ]
        ],
        'mainimage' => [
          'value' => (object)[],
          'name' => 'mediasSingle',
          'type' => 'media',
          'props' => [
            'label' => 'Image',
            'zone' => 'mainimage',
            'entity' => "Modules\Icommerce\Entities\PaymentMethod",
            'entityId' => null
          ]
        ],
        'init' => [
            'value' => 'Modules\Icommercepaypal\Http\Controllers\Api\IcommercePaypalApiController',
            'name' => 'init',
            'isFakeField' => true
        ],
        'clientId' => [
          'value' => null,
            'name' => 'clientId',
            'isFakeField' => true,
            'type' => 'input',
            'props' => [
                'label' => 'icommercepaypal::icommercepaypals.table.clientId'
            ]
        ],
        'clientSecret' => [
          'value' => null,
            'name' => 'clientSecret',
            'isFakeField' => true,
            'type' => 'input',
            'props' => [
                'label' => 'icommercepaypal::icommercepaypals.table.clientSecret'
            ]
        ],
        'mode' => [
            'value' => 'sandbox',
            'name' => 'mode',
            'isFakeField' => true,
            'type' => 'select',
            'props' => [
              'label' => 'icommercepaypal::icommercepaypals.table.mode',
              'useInput' => false,
              'useChips' => false,
              'multiple' => false,
              'hideDropdownIcon' => true,
              'newValueMode' => 'add-unique',
              'options' => [
                ['label' => 'Sandbox','value' => 'sandbox'],
                ['label' => 'Live','value' => 'live'],
              ]
            ]
        ],
        'minimunAmount' => [
          'value' => 3,
          'name' => 'minimunAmount',
          'isFakeField' => true,
          'type' => 'input',
          'props' => [
                'label' => 'icommerce::common.minimum Amount'
          ]
        ]

  ]

];