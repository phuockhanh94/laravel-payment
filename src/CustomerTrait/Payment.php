<?php

namespace GGPHP\Payment\CustomerTrait;

use Illuminate\Database\Eloquent\Model;
use \GGPHP\Payment\Facades\Payment as Billing;

class Payment
{
    /**
     * The card token of customer
     *
     * @var string
     */
    protected $cardToken;

    /**
     * Customer model
     *
     * @var mixed
     */
    protected $model;

    /**
     * Customer info array.
     *
     * @var array
     */
    protected $info = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->customer = $this->model->gatewayCustomer();
        $this->info = $this->customer ? $this->customer->info() : [];
    }

    /**
     * Create the customer
     *
     * @param  mixed $properties
     * @return Payment
     */
    public function create($properties = [])
    {
        // If exist customer in db, don't create new customer
        if (!$this->customer) {
            $this->customer = Billing::customer()->create($properties);
            $this->model->payment_id = $this->customer->getId();
            $this->model->save();
        }

        if ($this->customer) {
            $this->info = $this->customer->info();
        }

        return $this;
    }

    /**
     * Update the customer
     *
     * @param array $properties
     *
     * @return Payment
     */
    public function update($properties = [])
    {
        if (!$this->customer) {
            return $this;
        }

        $this->customer->update($properties);

        $this->info = $this->customer->info();

        return $this;
    }

    /**
     * Delete this customer
     *
     * @param array $properties
     *
     * @return Payment
     */
    public function delete($properties = [])
    {
        if (!$customer = $this->model->gatewayCustomer()) {
            return $this;
        }

        $customer->delete();

        $this->model->payment_id = null;
        $this->model->save();

        return $this;
    }

    /**
     * The credit card token to assign to a new customer.
     *
     * @param string $card_token
     *
     * @return Payment
     */
    public function withCardToken($cardToken)
    {
        $this->cardToken = $cardToken;

        return $this;
    }

    /**
     * Dynamically get values from the customer.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key = null)
    {
        return isset($this->info[$key]) ? $this->info[$key] : null;
    }
}
