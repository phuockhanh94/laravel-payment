<?php

namespace GGPHP\Payment\Gateways\Stripe;

use GGPHP\Payment\Gateways\ChargeInterface;
use Illuminate\Support\Arr;
use Stripe\Customer as StripeCustomer;
use Stripe\Charge as StripeCharge;

class Charge implements ChargeInterface
{
    /**
     * The gateway instance.
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Stripe customer object.
     *
     * @var StripeCustomer
     */
    protected $stripeCustomer;

    /**
     * Primary identifier.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Stripe charge object.
     *
     * @var Stripe_Charge
     */
    protected $stripeCharge;

    /**
     * Create a new Stripe charge instance.
     *
     * @param Gateway         $gateway
     * @param Stripe_Customer $customer
     * @param mixed           $id
     *
     * @return void
     */
    public function __construct(Gateway $gateway, StripeCustomer $customer = null, $id = null)
    {
        $this->gateway = $gateway;

        $this->stripeCustomer = $customer;

        if ($id) {
            $this->id = $id;
        }
    }

    /**
     * Gets info for a charge.
     *
     * @return array|null
     */
    public function info()
    {
        $this->getStripeCharge();

        if (!$this->stripeCharge) {
            return null;
        }

        return $this->stripeCharge->toArray();
    }

    /**
     * Create a new charge.
     *
     * @param int   $amount
     * @param array $properties
     *
     * @return Charge
     */
    public function create($amount, $properties = [])
    {
        $card = empty($properties['card']) ? null : $properties['card'];
        $properties = Arr::except($properties, ['card']);
        $charge = StripeCharge::create(array_merge($properties, [
            'amount'   => $amount,
            'customer' => $this->stripeCustomer->id,
            'currency' => empty($properties['currency']) ? 'usd' : $properties['currency'],
            'source'   => $card
        ]));

        $this->id = $charge->id;

        return $this;
    }

    /**
     * Capture a preauthorized charge.
     *
     * @param array $properties
     *
     * @return Charge
     */
    public function capture(array $properties = array())
    {

    }

    /**
     * Refund a charge.
     *
     * @param array $properties
     *
     * @return Charge
     */
    public function refund(array $properties = array())
    {

    }
    /**
     * Get Stripe charge
     *
     * @return array
     */
    public function getStripeCharge()
    {
        if (!$this->id) {
            return null;
        }

        if (!$this->stripeCharge) {
            $this->stripeCharge = StripeCharge::retrieve($this->id);
        }

        return $this->stripeCharge;
    }
}