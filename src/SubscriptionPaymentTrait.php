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
        if (!$this->everSubscribed()) {
            return null;
        }

        if ($customer = $this->customer()) {
            $customer = $customer->gatewayCustomer();
        }

        return Billing::subscription($this->billing_subscription, $customer);
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
        return new SubscriptionBillableTrait\Subscription($this, $plan);
    }
}
