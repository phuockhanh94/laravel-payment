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
     * Create a new subscription.
     *
     * @param mixed $plan
     * @param array $properties
     * @return Subscription
     * @throws \Exception
     */
    public function create($plan, $properties = [])
    {
        $trial_end = null;

        if (!empty($properties['trial_end'])) {
            $trial_end = strtotime($properties['trial_end']);
            if ($trial_end <= time()) {
                $trial_end = 'now';
            }
        }

        // Note: Stripe does not yet support specifying an existing card for a subscription.
        // This feature is coming in a future relase, however.
        // Currently you can only specify a card token and the same card is used for all
        // customer subscriptions.
        $stripe_subscriptions = $this->stripeCustomer->subscriptions;

        if ( ! $stripe_subscriptions) {
            throw new \Exception("Stripe Customer does not exist.");
        }

        $stripe_subscription = $stripe_subscriptions->create(array(
            // 'plan'      => $plan,
            'items' => [
                [
                    'price' => $plan,
                    'quantity'  => $properties['quantity'] ? $properties['quantity'] : null,
                ]
            ],

            'trial_end' => 'now',
            'coupon'    => $properties['coupon'] ? $properties['coupon'] : null,
            // 'source'    => $properties['card_token'] ? $properties['card_token'] : null,
        ));
        dd($stripe_subscription);

        $this->id = $stripe_subscription->id;

        return $this;
   }
}
