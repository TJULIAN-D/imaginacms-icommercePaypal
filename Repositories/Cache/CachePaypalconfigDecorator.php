<?php

namespace Modules\Icommercepaypal\Repositories\Cache;

use Modules\Icommercepaypal\Repositories\PaypalconfigRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePaypalconfigDecorator extends BaseCacheDecorator implements PaypalconfigRepository
{
    public function __construct(PaypalconfigRepository $paypalconfig)
    {
        parent::__construct();
        $this->entityName = 'icommercepaypal.paypalconfigs';
        $this->repository = $paypalconfig;
    }
}
