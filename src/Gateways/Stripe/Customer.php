<?php

namespace GGPHP\Payment\Gateways\Stripe;

use GGPHP\Payment\Gateways\GatewayInterface;
use GGPHP\Payment\Gateways\CustomerInterface;
use Stripe\Customer as StripeCustomer;

class Customer implements CustomerInterface
{
    /**
     * The gateway instance.
     *
     * @var gateway
     */
    protected $gateway;

    /**
     * Customer id
     *
     * @var mixed
     */
    protected $id;

     /**
     * Stripe customer object.
     *
     * @var StripeCustomer
     */
    protected $stripeCustomer;

    /**
     * Create a new instance.
     *
     * @param  mixed $gateway
     * @return void
     */
    public function __construct(GatewayInterface $gateway, $id = null)
    {
        $this->gateway = $gateway;
        if ($id) {
            $this->id = $id;
        }
    }

    /**
     * Set id for a customer.
     *
     * @param  mixed $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id for a customer.
     *
     * @return void
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets info for a customer.
     *
     * @return array|null
     */
    public function info()
    {
        $this->getStripeCustomer();

        if (!$this->stripeCustomer) {
            return null;
        }

        return $this->stripeCustomer->toArray();
    }

    /**
     * Create customer
     *
     * @param  mixed $properties
     * @return Customer
     */
    public function create($properties = [])
    {
        $stripeCustomer = StripeCustomer::create($properties);

        $this->id = $stripeCustomer->id;

        return $this;
    }

    /**
     * Update customer
     *
     * @param  mixed $properties
     * @return Customer
     */
    public function update($properties = [])
    {
        $this->getStripeCustomer();
        foreach ($properties as $key => $value) {
            $this->stripeCustomer->$key = $value;
        }

        $this->stripeCustomer->save();

        return $this;
    }

    /**
     * Delete a customer.
     *
     * @return Customer
     */
    public function delete()
    {
        $this->getStripeCustomer();
        $this->stripeCustomer->delete();
        $this->stripeCustomer = null;

        return $this;
    }

    public function subscriptions()
    {

    }

    /**
     * cards
     *
     * @return void
     */
    public function cards()
    {

    }

    /**
     * Get card
     *
     * @param  mixed $id
     * @return void
     */
    public function card($id = null)
    {
        $this->getStripeCustomer();
        return new Card($this->gateway, $this->stripeCustomer, $id);
    }

    public function invoices()
    {

    }

    public function invoice($id = null)
    {

    }

    public function charges()
    {

    }
    public function charge($id = null)
    {
        $this->getStripeCustomer();

        return new Charge($this->gateway, $this->stripeCustomer, $id);
    }

    /**
     * Get Stripe customer
     *
     * @param  mixed $properties
     * @return array
     */
    public function getStripeCustomer()
    {
        if (!$this->id) {
            return null;
        }

        if (!$this->stripeCustomer) {
            $this->stripeCustomer = StripeCustomer::retrieve($this->id);
        }

        return $this->stripeCustomer;
    }
}
