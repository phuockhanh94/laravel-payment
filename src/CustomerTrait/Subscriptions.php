<?php

namespace GGPHP\Payment\CustomerTrait;

use Illuminate\Database\Eloquent\Model;
use \GGPHP\Payment\Facades\Payment as Billing;
use Illuminate\Support\Arr;

class Subscriptions
{

    /**
     * Customer model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Query limit.
     *
     * @var int
     */
    protected $limit;

    /**
     * Query offset.
     *
     * @var int
     */
    protected $offset;

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
     * Create a new CustomerBillableTrait Subscriptions instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $plan
     *
     * @return void
     */
    public function __construct(Model $model, $plan = null)
    {
        $this->model = $model;
        $this->plan = $plan;
    }

    /**
     * Create this subscription in the billing gateway.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $properties
     *
     * @return GGPHP\Payment\CustomerTrait\Subscription
     */
    public function create($properties = [])
    {
        if (!$this->model->alreadyExistPayment()) {
            $this->model->payment()->create();
        }

        if ($this->cardToken) {
            $this->card = $this->model->card()->create($this->cardToken)->id;
            $this->cardToken = null;
        }
        $this->subscription = Billing::subscription(null, $this->model->gatewayCustomer())
        ->create($this->plan, array_merge($properties, [
            'trial_end' => $this->skipTrial ? date('Y-m-d H:i:s') : (isset($properties['trial_end']) ? $properties['trial_end'] : null),
            'coupon'        => $this->coupon,
            'quantity'      => isset($properties['quantity']) ? $properties['quantity'] : 1,
            'card_token'    => $this->cardToken,
            'card'          => $this->card,
        ]));

        $info = $this->subscription->info();
        $active = $info['status'] != 'canceled' && !$info['cancel_at_period_end'];

        if (method_exists($this->model, 'subscriptionsModel')) {
            $this->model->subscriptionsModel()->create([
                'payment_active' => $active ? 1 : 0,
                'payment_subscription_id' => $this->subscription->getId(),
                'payment_free' => 0,
                'payment_plan' => $info['plan']['id'],
                'payment_amount' => $info['plan']['amount'],
                'payment_interval' => $info['plan']['interval'],
                'payment_quantity' => $info['quantity'],
                'payment_card' => $info['default_source'],
                'payment_trial_ends_at' => $active && $info['trial_end'] ? date('Y-m-d H:i:s', $info['trial_end']) : null,
                'payment_subscription_ends_at' => date('Y-m-d H:i:s', $info['current_period_end']),
                'payment_subscription_discounts' => json_encode($info['discount']),
            ]);
        }

        return $this;
    }

    /**
     * The coupon to apply to a new subscription.
     *
     * @param string $coupon
     *
     * @return Subscriptions
     */
    public function withCoupon($coupon)
    {
        $this->coupon = $coupon;
        return $this;
    }

    /**
     * The credit card token to assign to a new subscription.
     *
     * @param string $card_token
     *
     * @return Subscriptions
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
     * @return Subscriptions
     */
    public function withCard($card)
    {
        $this->card = is_array($card) ? Arr::get($card, 'id') : $card;
        return $this;
    }

    /**
     * Indicate that no trial should be enforced on the operation.
     *
     * @return Subscriptions
     */
    public function skipTrial()
    {
        $this->skipTrial = true;
        return $this;
    }
}
