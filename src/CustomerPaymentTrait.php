<?php

namespace GGPHP\Payment;

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
     * Determine if the entity is a Billing customer.
     *
     * @return bool
     */
    public function alreadyExistPayment()
    {
        return !empty($this->payment_id);
    }
}
