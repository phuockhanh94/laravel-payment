<?php

namespace GGPHP\Payment\Gateways\Stripe;

use GGPHP\Payment\Gateways\CardInterface;
use GGPHP\Payment\Gateways\GatewayInterface;
use Illuminate\Support\Arr;
use Stripe\Customer as StripeCustomer;
use Stripe\Card as Stripe_Card;

class Card implements CardInterface
{
    /**
     * The gateway instance.
     *
     * @var gateway
     */
    protected $gateway;

    /**
     * Card id
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
     * Stripe card object.
     *
     * @var Stripe_Card
     */
    protected $stripeCard;

    /**
     * Create a new Stripe card instance.
     *
     * @param Gateway $gateway
     * @param Stripe_Customer $customer
     * @param mixed $id
     *
     */
    public function __construct(GatewayInterface $gateway, StripeCustomer $customer, $id = null)
    {
        $this->gateway = $gateway;

        $this->stripeCustomer = $customer;

        if ($id) {
            $this->id = $id;
        }
    }

    /**
     * Create a new card.
     *
     * @param string $card_token
     *
     * @return Card
     */
    public function create($cardToken)
    {
        $stripeCard = $this->stripeCustomer->sources->create(array(
            'source' => $cardToken,
        ));

        $this->stripeCard = $stripeCard;
        $this->id = $stripeCard->id;

        return $this;
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
     * Gets info for a card.
     *
     * @return array|null
     */
    public function info()
    {
        if (!$this->id || !$this->stripeCustomer) {
            return null;
        }

        if (!$this->stripeCard) {
            $this->stripeCard = $this->stripeCustomer->sources->retrieve($this->id);
        }

        if (!$this->stripeCard) {
            return null;
        }

        return  $this->stripeCard->toArray();
    }

    /**
     * Update a card.
     *
     * @param array $properties
     *
     * @return CardInterface
     */
    public function update($properties = [])
    {
        if (!$this->id || !$this->stripeCustomer) {
            return null;
        }

        if (!$this->stripeCard) {
            $this->stripeCard = $this->stripeCustomer->sources->retrieve($this->id);
        }

        if (!$this->stripeCard) {
            return null;
        }

        foreach ($properties as $key => $value) {
            $this->stripeCard->$key = $value;
        }

        $this->stripeCard->save();

        return $this;
    }

    /**
     * Delete a card.
     *
     * @return CardInterface
     */
    public function delete()
    {
        if (!$this->id || !$this->stripeCustomer) {
            return null;
        }

        if (!$this->stripeCard) {
            $this->stripeCard = $this->stripeCustomer->sources->retrieve($this->id);
        }

        if (!$this->stripeCard) {
            return null;
        }
        $this->stripeCard->delete();
        $this->stripeCard = null;

        return $this;
    }
}
