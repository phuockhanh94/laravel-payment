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
    }

    /**
     * Create customer
     *
     * @param  mixed $properties
     * @return void
     */
    public function create($properties = [])
    {
        // If exist customer in db, don't create new customer
        if ($this->model->gatewayCustomer()) {
            $customer = $this->model->gatewayCustomer();
        } else {
            $customer = Billing::customer()->create($properties);
            $this->model->payment_id = $customer->getId();
            $this->model->save();
        }

        if ($customer) {
            $this->info = $customer->info($properties);
        }

        return $this;
    }

    /**
     * Dynamically get values from the customer.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key = null)
    {
        return isset($this->info[$key]) ? $this->info[$key] : $this->info;
    }
}