<?php

namespace Modules\Icommercepaypal\Repositories\Cache;

use Modules\Icommercepaypal\Repositories\IcommercePaypalRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheIcommercePaypalDecorator extends BaseCacheDecorator implements IcommercePaypalRepository
{
    public function __construct(IcommercePaypalRepository $icommercepaypal)
    {
        parent::__construct();
        $this->entityName = 'icommercepaypal.icommercepaypals';
        $this->repository = $icommercepaypal;
    }
}
