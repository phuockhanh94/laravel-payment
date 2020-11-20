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
     * Gets list charge by Customer
     *
     * @return array|null
     */
    public function all()
    {
        $charges = StripeCharge::all(['customer' => $this->stripeCustomer->id]);

        $chargesAray = [];
        foreach ($charges->data as $charge) {
            $chargesAray[] = new Charge($this->gateway, $this->stripeCustomer, $charge->id);
        }

        return $chargesAray;
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
            'customer' => empty($properties['customer']) ? $this->stripeCustomer->id : $properties['customer'],
            'currency' => empty($properties['currency']) ? 'usd' : $properties['currency'],
            'source'   => $card
        ]));

        $this->id = $charge->id;

        return $this;
    }

    /**
     * Update a charge.
     *
     * @param array $properties
     *
     * @return ChargeInterface
     */
    public function update($properties = [])
    {
        if (!$this->getStripeCharge()) {
            return null;
        }

        foreach ($properties as $key => $value) {
            $this->stripeCharge->$key = $value;
        }

        $this->stripeCharge->save();

        return $this;
    }

    /**
     * Capture a charge.
     *
     * @param array $properties
     *
     * @return Charge
     */
    public function capture($properties = [])
    {
        if (!$this->getStripeCharge()) {
            return null;
        }

        $this->stripeCharge->capture($properties);
        $this->stripeCharge = null;

        return $this;
    }

    /**
     * Refund a charge.
     *
     * @param array $properties
     *
     * @return Charge
     */
    public function refund($properties = [])
    {
        if (!$this->getStripeCharge()) {
            return null;
        }

        $this->stripeCharge->refunds->create($properties);
        $this->stripeCharge = null;

        return $this;
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
