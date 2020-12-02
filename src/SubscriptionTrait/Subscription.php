<?php

namespace GGPHP\Payment\SubscriptionTrait;

use LinkThrow\Billing\Facades\Billing;
use Illuminate\Support\Arr;
use Exception;

class Subscription
{
    /**
     * Subscription model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Charge gateway instance.
     *
     * @var \LinkThrow\Billing\Gateways\SubscriptionInterface
     */
    protected $subscription;

    /**
     * Subscription info array.
     *
     * @var array
     */
    protected $info;

    /**
     * Subscription plan.
     *
     * @var mixed
     */
    protected $plan;

    /**
     * The coupon to apply to the subscription.
     *
     * @var string
     */
    protected $coupon;

    /**
     * The credit card token to assign to the subscription.
     *
     * @var string
     */
    protected $cardToken;

    /**
     * The credit card id to assign to the subscription.
     *
     * @var string
     */
    protected $card;

    /**
     * Whether or not to force skip the trial period.
     *
     * @var bool
     */
    protected $skipTrial;

    /**
     * Whether or not this subscription should be free (not stored in billing gateway).
     *
     * @var bool
     */
    protected $isFree;

    /**
     * Create a new SubscriptionPaymentTrait Subscription instance.
     *
     * @param \Illuminate\Database\Eloquent\Model            $model
     * @param mixed                                          $plan
     * @param array                                          $info
     *
     * @return void
     */
    public function __construct(Model $model, $plan = null, $info = [])
    {
        $this->model = $model;
        $this->plan = $plan ? $plan : $this->model->payment_plan;
        $this->subscription = $this->model->gatewaySubscription();
        $this->info = $info;
    }

    /**
     * Create this subscription in the payment gateway.
     *
     * @param array $properties
     *
     * @return Subscription
     */
    public function create(array $properties = array())
    {
        if ($this->model->billingIsActive()) {
            return $this;
        }

        if ($customer = $this->model->customer()) {
            if (!$customer->readyForBilling()) {
                if ($this->cardToken) {
                    $customer->billing()->withCardToken($this->cardToken)->create($properties);
                    if (!empty($customer->billing_cards)) {
                        $this->cardToken = null;
                    }
                }
                else {
                    $customer->billing()->create($properties);
                }
            }
            else if ($this->cardToken) {
                $this->card = $customer->creditcards()->create($this->cardToken)->id;
                $this->cardToken = null;
            }
        }

        $this->subscription = Billing::subscription(null, $customer ? $customer->gatewayCustomer() : null)
            ->create($this->plan, array_merge($properties, array(
                'trial_ends_at' => $this->skipTrial ? date('Y-m-d H:i:s') : $this->model->billing_trial_ends_at,
                'coupon'        => $this->coupon,
                'quantity'      => Arr::get($properties, 'quantity', 1),
                'cardToken'    => $this->cardToken,
                'card'          => $this->card,
            )));

        $this->refresh();

        return $this;
    }

    /**
     * The coupon to apply to a new subscription.
     *
     * @param string $coupon
     *
     * @return Subscription
     */
    public function withCoupon($coupon)
    {
        $this->coupon = $coupon;
        return $this;
    }

    /**
     * The credit card token to assign to a new subscription.
     *
     * @param string $cardToken
     *
     * @return Subscription
     */
    public function withCardToken($cardToken)
    {
        $this->cardToken = $cardToken;
        return $this;
    }

    /**
     * The credit card id or array to assign to a new subscription.
     *
     * @param string|array $card
     *
     * @return Subscription
     */
    public function withCard($card)
    {
        $this->card = is_array($card) ? Arr::get($card, 'id') : $card;
        return $this;
    }

    /**
     * Indicate that no trial should be enforced on the operation.
     *
     * @return Subscription
     */
    public function skipTrial()
    {
        $this->skipTrial = true;
        return $this;
    }

    /**
     * Indicate that this subscription should be free and not stored in the billing gateway.
     *
     * @return Subscription
     */
    public function isFree()
    {
        $this->isFree = true;
        return $this;
    }

    /**
     * Dynamically check a values existence from the subscription.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->info[$key]);
    }

    /**
     * Dynamically get values from the subscription.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->info[$key]) ? $this->info[$key] : null;
    }
}
