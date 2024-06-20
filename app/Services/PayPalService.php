<?php

namespace App\Services;

use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalService
{
    private $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('paypal'));
    }

    public function createFlexiblePlan($data)
    {
        $this->provider->getAccessToken();
        return $this->provider->createPlan($data);
    }

    public function updatePlan($planId, $data)
    {
        $this->provider->getAccessToken();
        return $this->provider->updatePlan($planId, $data);
    }

    public function activatePlan($planId)
    {
        $this->provider->getAccessToken();
        return $this->provider->activatePlan($planId);
    }
}