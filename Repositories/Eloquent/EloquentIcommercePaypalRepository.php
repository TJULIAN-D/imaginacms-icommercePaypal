<?php

namespace Modules\Icommercepaypal\Repositories\Eloquent;

use Modules\Icommercepaypal\Repositories\IcommercePaypalRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentIcommercePaypalRepository extends EloquentBaseRepository implements IcommercePaypalRepository
{
    
    /**
     * Update Payment Method
     *
     * @param $model
     * @param $data
     * @return 
     */
    public function update($model, $data){

        //Get data
        $requestimage = $data['mainimage'];
        $requestclientId = $data['clientId'];
        $requestclientSecret = $data['clientSecret'];
        $requestmode = $data['mode'];

        // Delete attributes
        unset($data['mainimage']);
        unset($data['clientId']);
        unset($data['clientSecret']);
        unset($data['mode']);

        // Image
        if(($requestimage==NULL) || (!empty($requestimage)) ){
            $requestimage = $this->saveImage($requestimage,"assets/icommercepaypal/1.jpg");
        }
        $options['mainimage'] = $requestimage;

        // Extra data
        $options['clientId'] = $requestclientId;
        $options['clientSecret'] = $requestclientSecret;
        $options['mode'] = $requestmode;

        // Extra data in Options
        $data['options'] = $options;

        $model->update($data);

        return $model;

    }

    /**
     * Save Image
     *
     * @param  $value
     * @param  $destination
     * @return 
     */
    public function saveImage($value,$destination_path)
    {

        $disk = "publicmedia";

        //Defined return.
        if(ends_with($value,'.jpg')) {
            return $value;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value);
            // resize and prevent possible upsizing

            $image->resize(config('asgard.iblog.config.imagesize.width'), config('asgard.iblog.config.imagesize.height'), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            if(config('asgard.iblog.config.watermark.activated')){
                $image->insert(config('asgard.iblog.config.watermark.url'), config('asgard.iblog.config.watermark.position'), config('asgard.iblog.config.watermark.x'), config('asgard.iblog.config.watermark.y'));
            }
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path, $image->stream('jpg','80'));


            // Save Thumbs
            \Storage::disk($disk)->put(
                str_replace('.jpg','_mediumThumb.jpg',$destination_path),
                $image->fit(config('asgard.iblog.config.mediumthumbsize.width'),config('asgard.iblog.config.mediumthumbsize.height'))->stream('jpg','80')
            );

            \Storage::disk($disk)->put(
                str_replace('.jpg','_smallThumb.jpg',$destination_path),
                $image->fit(config('asgard.iblog.config.smallthumbsize.width'),config('asgard.iblog.config.smallthumbsize.height'))->stream('jpg','80')
            );

            // 3. Return the path
            return $destination_path;
        }

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($destination_path);

            // set null in the database column
            return null;
        }
    }

}
