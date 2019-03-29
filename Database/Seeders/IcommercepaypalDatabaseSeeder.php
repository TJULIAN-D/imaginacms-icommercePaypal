<?php

namespace Modules\Icommercepaypal\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Icommerce\Entities\PaymentMethod;

class IcommercepaypalDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $options['init'] = "Modules\Icommercepaypal\Http\Controllers\Api\IcommercePaypalApiController";
        $options['mainimage'] = null;
        $options['clientid'] = "";
        $options['clientsecret'] = "";
        $options['mode'] = "sandbox";
        
        $titleTrans = 'icommercepaypal::icommercepaypals.single';
        $descriptionTrans = 'icommercepaypal::icommercepaypals.description';

        foreach (['en', 'es'] as $locale) {

            if($locale=='en'){
                $params = array(
                    'title' => trans($titleTrans),
                    'description' => trans($descriptionTrans),
                    'name' => config('asgard.icommercepaypal.config.paymentName'),
                    'status' => 0,
                    'options' => $options
                );

                $paymentMethod = PaymentMethod::create($params);
                
            }else{

                $title = trans($titleTrans,[],$locale);
                $description = trans($descriptionTrans,[],$locale);

                $paymentMethod->translateOrNew($locale)->title = $title;
                $paymentMethod->translateOrNew($locale)->description = $description;

                $paymentMethod->save();
            }

        }// Foreach


    }
}
