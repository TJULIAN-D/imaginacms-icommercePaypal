<?php

namespace Modules\Icommercepaypal\Entities;

class Paypalconfig
{
    private $description;
    private $clientid;
    private $clientsecret;
    private $currency;
    private $endpoint;
    private $mode;
    private $image;
    private $status;
    public function __construct()
    {
        $this->description = setting('icommercePaypal::description');
        $this->clientid = setting('icommercePaypal::clientid');
        $this->clientsecret = setting('icommercePaypal::clientsecret');
        $this->currency = setting('icommercePaypal::currency');
        $this->endpoint = setting('icommercePaypal::endpoint');
        $this->mode = setting('icommercePaypal::mode');
        $this->image = setting('icommercePaypal::image');
        $this->status = setting('icommercePaypal::status');
    }

    public function getData()
    {
        return (object) [
            'description' => $this->description,
            'clientid' => $this->clientid,
            'clientsecret' => $this->clientsecret,
            'currency' => $this->currency,
            'endpoint' => $this->endpoint,
            'mode' => $this->mode,
            'image' => url($this->image),
            'status' => $this->status
        ];
    }
}
