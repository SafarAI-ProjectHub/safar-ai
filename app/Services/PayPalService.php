<?php

namespace App\Services;

use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalService
{
    protected $paypal;

    public function __construct()
    {
        $this->paypal = new PayPalClient;
        $this->paypal->setApiCredentials(config('paypal'));
        $this->paypal->getAccessToken();
    }


    public function createProduct(string $name, string $description)
    {
        $response = $this->paypal->createProduct([
            'name' => $name,
            'description' => $description,
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
        ]);

        return $response;
    }

    public function createPlan(string $productId, string $name, string $description, float $price)
    {
        $response = $this->paypal->createPlan([
            'product_id' => $productId,
            'name' => $name,
            'description' => $description,
            'status' => 'ACTIVE',
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => 'MONTH',
                        'interval_count' => 1,
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => $price,
                            'currency_code' => 'USD',
                        ],
                    ],
                ],
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => '0',
                    'currency_code' => 'USD',
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3,
            ],
        ]);

        return $response;
    }

    public function createSubscription(string $planId, string $subscriberEmail, string $customId)
    {
        try {
            $response = $this->paypal->createSubscription([
                'plan_id' => $planId,
                'subscriber' => [
                    'custom_id' => $customId,
                    'email_address' => $subscriberEmail,
                ],

                'application_context' => [
                    'brand_name' => config('app.name'),
                    'locale' => 'en-US',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'payment_method' => [
                        'payer_selected' => 'PAYPAL',
                        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                    ],
                    'return_url' => route('paypal.return'),
                    'cancel_url' => route('paypal.cancel'),
                    'custom_id' => $customId,
                ],
            ]);
            // dd($response);
            return $response;
        } catch (\Exception $e) {
            \Log::error('Error creating PayPal Subscription:', ['message' => $e->getMessage()]);
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }


    public function captureSubscription(string $subscriptionId)
    {
        $response = $this->paypal->captureSubscription($subscriptionId);

        return $response;
    }

    public function reactivateSubscription(string $subscriptionId, $reason = 'Reactivating subscription')
    {
        $response = $this->paypal->activateSubscription($subscriptionId, $reason);

        return $response;
    }

    public function cancelSubscription(string $subscriptionId, $reason = 'Not specified')
    {
        $response = $this->paypal->cancelSubscription($subscriptionId, $reason);

        return $response;
    }

    public function updateProduct(string $productId, array $data)
    {
        $patchData = [];
        foreach ($data as $key => $value) {
            $patchData[] = [
                'op' => 'replace',
                'path' => '/' . $key,
                'value' => $value,
            ];
        }

        return $this->paypal->updateProduct($productId, $patchData);
    }

    public function updatePlanDescription(string $planId, string $description)
    {
        $patchData = [
            [
                'op' => 'replace',
                'path' => '/description',
                'value' => $description,
            ],
        ];

        return $this->paypal->updatePlan($planId, $patchData);
    }

    public function updatePlanPricingScheme(string $planId, float $price, string $currency = 'USD', int $sequence = 1)
    {
        $pricingSchemeData = [
            'pricing_schemes' => [
                [
                    'billing_cycle_sequence' => $sequence,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => $price,
                            'currency_code' => $currency
                        ]
                    ]
                ]
            ]
        ];

        return $this->paypal->updatePlanPricing($planId, $pricingSchemeData);
    }

}