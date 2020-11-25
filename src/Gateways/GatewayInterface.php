<?php

namespace GGPHP\Payment\Gateways;

interface GatewayInterface
{
    /**
     * Get customer instance
     *
     * @param  mixed $id
     * @return void
     */
    public function customer($id = null);

    /**
     * Get subscription instance
     *
     * @param  mixed $id
     * @param  mixed $customer
     * @return void
     */
    public function subscription($id = null, CustomerInterface $customer = null);

    /**
     * Charge
     *
     * @param  mixed $id
     * @param  mixed $customer
     * @return void
     */
    public function charge($id = null, CustomerInterface $customer = null);
}
