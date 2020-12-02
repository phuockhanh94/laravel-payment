<?php

namespace GGPHP\Payment;

use \GGPHP\Payment\Facades\Payment as Billing;
use LogicException;

trait SubscriptionPaymentTrait
{
    /**
     * Return the gateway subscription object for this model.
     *
     * @return LinkThrow\Billing\Gateways\SubscriptionInterface
     */
    public function gatewaySubscription()
    {
        if (!$this->alreadySubscribed()) {
            return null;
        }

        if ($customer = $this->getCustomer()) {
            $customer = $customer->gatewayCustomer();
        }

        return Billing::subscription($this->payment_subscription_id, $customer);
    }

    /**
     * Return the subscription billing helper object.
     *
     * @param mixed $plan
     *
     * @return SubscriptionBillableTrait\Subscription
     */
    public function subscription($plan = null)
    {
        return new SubscriptionTrait\Subscription($this, $plan);
    }

     /**
     * Determine if the entity is a Billing customer.
     *
     * @return bool
     */
    public function alreadySubscribed()
    {
        return !empty($this->payment_subscription_id);
    }

    /**
     * The method customer define relationship between customer & subscription
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getCustomer()
    {
        if (method_exists($this, 'customer')) {
            return $this->customer();
        }

        return null;
    }
}
