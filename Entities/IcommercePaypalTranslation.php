<?php

namespace Modules\Icommercepaypal\Entities;

use Illuminate\Database\Eloquent\Model;

class IcommercePaypalTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'icommercepaypal__icommercepaypal_translations';
}
