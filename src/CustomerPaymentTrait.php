<?php

namespace GGPHP\Payment;

use \GGPHP\Payment\Facades\Payment as Billing;

trait CustomerPaymentTrait
{
    /**
     * Get instance CustomerTrait\Billing
     *
     * @return GGPHP\Payment\CustomerTrait\Payment
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

    /**
     * Get instance CustomerTrait\Card
     *
     * @return GGPHP\Payment\CustomerTrait\Card
     */
    public function card()
    {
        return new CustomerTrait\Card($this);
    }

    /**
     * Getter for payment_cards property.
     *
     * @param string $value
     *
     * @return array
     */
    public function getPaymentCardsAttribute($value)
    {
        return $value ? json_decode($value, true) : array();
    }

    /**
     * Setter for payment_cards property.
     *
     * @param array $value
     *
     * @return void
     */
    public function setPaymentCardsAttribute($value)
    {
        $this->attributes['payment_cards'] = empty($value) ? null : json_encode($value);
    }

    /**
     * Get instance CustomerTrait\Charges
     *
     * @return CustomerBillableTrait\Charges
     */
    public function charges()
    {
        return new CustomerTrait\Charge($this);
    }
}
