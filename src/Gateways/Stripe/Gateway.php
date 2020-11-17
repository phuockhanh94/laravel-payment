<?php

namespace GGPHP\Payment\Gateways\Stripe;

use GGPHP\Payment\Gateways\GatewayInterface;
use GGPHP\Payment\Gateways\CustomerInterface;
use Illuminate\Support\Facades\Config;
use Stripe\Stripe;

class Gateway implements GatewayInterface
{
    /**
     * Create a new instance.
     *
     * @param  mixed $gateway
     * @return void
     */
    public function __construct($gateway = null)
    {
        if (!$gateway) {
            $gateway = Config::get('payment.gateways.stripe');
        }

        if ($gateway['test_mode'] == true) {
            $secrectKey = $gateway['secrect_test_key'];
        } else {
            $secrectKey = $gateway['secrect_key'];
        }
        Stripe::setApiKey($secrectKey);
    }

    /**
     * Get Customer
     *
     * @param  mixed $id
     * @return Customer instance
     */
    public function customer($id = null)
    {
        return new Customer($this, $id);
    }

    /**
     * Subscription
     *
     * @param  mixed $id
     * @param  mixed $customer
     * @return void
     */
    public function subscription($id = null, CustomerInterface $customer = null)
    {
        return new Subscription($this, $id);
    }

    public function charge($id = null, CustomerInterface $customer = null)
    {
        if ($customer) {
            $customer = $customer->getStripeCustomer();
        }

        return new Charge($this, $customer, $id);
    }
}
