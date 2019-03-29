<?php

namespace Modules\Icommercepaypal\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class IcommercePaypal extends Model
{
    use Translatable;

    protected $table = 'icommercepaypal__icommercepaypals';
    public $translatedAttributes = [];
    protected $fillable = [];
}
