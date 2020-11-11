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
    public function info($properties = [])
    {
        if (!$this->id) {
            return null;
        }

        if (!$this->stripeCustomer) {
            $this->stripeCustomer = StripeCustomer::retrieve($this->id);
        }

        if (!$this->stripeCustomer) {
            return null;
        }

        $response = array_merge($this->getProperties($properties), [
            'id' => $this->stripeCustomer->id
        ]);

        return $response;
    }

    public function create($properties = [])
    {
        $stripeCustomer = StripeCustomer::create($properties);

        $this->id = $stripeCustomer->id;

        return $this;
    }

    public function update(array $properties = array())
    {

    }

    public function delete()
    {

    }
    public function subscriptions()
    {

    }

    public function cards()
    {

    }

    public function card($id = null)
    {

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

    }

    /**
     * Get Properties
     *
     * @param  mixed $properties
     * @return array
     */
    public function getProperties($properties = [])
    {
        $response = [];
        if (!empty($properties)) {
            foreach ($properties as $property => $value) {
                $response[$property] = isset($this->stripeCustomer->$property) ? $this->stripeCustomer->$property : null;
            }
        }

        return $response;
    }
}