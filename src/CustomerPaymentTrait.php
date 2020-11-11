<?php

namespace GGPHP\Payment;

use \GGPHP\Payment\Facades\Payment as Billing;

trait CustomerPaymentTrait
{
    /**
     * Get instance CustomerBillableTrait\Billing
     *
     * @return GGPHP\Payment\Helper\Payment
     */
    public function payment()
    {
        return new CustomerTrait\Payment($this);
    }

    /**
     * Return the gateway customer object for this user.
     *
     * @return GGPHP\Payment\Gateways\CustomerInterface
     */
    public function gatewayCustomer()
    {
        if (!$this->alreadyExistPayment()) {
            return null;
        }

        return Billing::customer($this->payment_id);
    }

     /**
     * Determine if the entity is a Billing customer.
     *
     * @return bool
     */
    public function alreadyExistPayment()
    {
        return !empty($this->payment_id);
    }
}
