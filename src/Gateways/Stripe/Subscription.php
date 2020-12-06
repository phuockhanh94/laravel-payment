<?php

namespace GGPHP\Payment\Gateways\Stripe;

use LinkThrow\Billing\Gateways\CustomerInterface;
use Stripe\Customer as StripeCustomer;
use GGPHP\Payment\Gateways\GatewayInterface;

class Subscription
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
     * Stripe subscription object.
     *
     * @var StripeCustomer
     */
    protected $stripeSubscription;

    /**
     * Create a new instance.
     *
     * @param  mixed $gateway
     * @return void
     */
    public function __construct(GatewayInterface $gateway, StripeCustomer $customer = null, $id = null)
    {
        $this->gateway = $gateway;
        $this->stripeCustomer = $customer;
        if ($id) {
            $this->id = $id;
        }
    }

    /**
     * Get id for a subscription.
     *
     * @return void
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Gets info for a subscription.
     *
     * @return array|null
     */
    public function info()
    {
        if (!$this->id || !$this->stripeCustomer) {
            return null;
        }

        if (!$this->stripeSubscription) {
            $this->stripeSubscription = $this->stripeCustomer->subscriptions->retrieve($this->id);
        }

        if (!$this->stripeSubscription) {
            return null;
        }

        return $this->stripeSubscription->toArray();
    }

    /**
     * Create a new subscription.
     *
     * @param mixed $plan
     * @param array $properties
     * @return Subscription
     * @throws \Exception
     */
    public function create($plan, $properties = [])
    {
        $trialEnd = null;

        if (!empty($properties['trial_end'])) {
            $trialEnd = strtotime($properties['trial_end']);
            if ($trialEnd <= time()) {
                $trialEnd = 'now';
            }
        }

        $stripeSubscriptions = $this->stripeCustomer->subscriptions;

        if ( ! $stripeSubscriptions) {
            throw new \Exception("Stripe Customer does not exist.");
        }

        $stripeSubscription = $stripeSubscriptions->create([
            'items' => [
                [
                    'price' => $plan,
                    'quantity'  => isset($properties['quantity']) ? $properties['quantity'] : null,
                    'tax_rates'  => isset($properties['tax_rates']) ? $properties['tax_rates'] : null,
                ]
            ],
            'trial_end' => $trialEnd,
            'coupon'    => isset($properties['coupon']) ? $properties['coupon'] : null,
            'source'    => isset($properties['card_token']) ? $properties['card_token'] : null,
        ]);

        dd($stripeSubscription);
        $this->stripeSubscription = $stripeSubscription;
        $this->id = $stripeSubscription->id;

        return $this;
    }
}
