<?php

namespace App\Services;

use Braintree\Gateway;

class BraintreeGateway
{

    public $gateway;
    public function __construct()
    {
        $this->gateway = new Gateway([
            'environment' => config('services.braintree.environment'),
            'merchantId' => config("services.braintree.merchant_id"),
            'publicKey' => config("services.braintree.public_key"),
            'privateKey' => config("services.braintree.private key")
        ]);
    }
}
