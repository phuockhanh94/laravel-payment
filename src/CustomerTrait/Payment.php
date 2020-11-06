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
        // if ($this->model->alreadyExistPayment()) {
        //     return $this;
        // }

        $customer = Billing::customer()->create($properties);

        if ($customer) {
            // dd(323);
            $this->model->payment_id = $customer->getId() ?? null;
            $this->model->save();
            $this->info = $customer->info($properties);
        }

        return $this;
    }

    /**
     * Convert this instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->info;
    }

    /**
     * Dynamically get values from the customer.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->info[$key]) ? $this->info[$key] : null;
    }

}
