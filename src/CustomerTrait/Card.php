<?php

namespace GGPHP\Payment\CustomerTrait;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Card
{
    /**
     * Customer model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Credit card.
     *
     * @var \LinkThrow\Billing\Gateways\CardInterface
     */
    protected $card;

    /**
     * Card info array.
     *
     * @var array
     */
    protected $info;

    /**
     * Create a new CustomerBillableTrait Creditcard instance.
     *
     * @param \Illuminate\Database\Eloquent\Model    $model
     * @param \LinkThrow\Billing\Gateways\CardInterface $card
     * @param array                                  $info
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        // $this->card = $card;

        // if (empty($info)) {
        //     $info = $card->info();
        // }

        // $this->info = $info;
    }

    /**
     * Create the card
     *
     * @param  mixed $properties
     * @return Payment
     */
    public function create($cardToken = [])
    {
        // If exist customer in db, don't create new customer
        if (!$customer = $this->model->gatewayCustomer()) {
            return;
        }
        $card = $customer->card()->create($cardToken);

        $this->model->billing_cards = array_merge($this->model->billing_cards, [ [$card->getId()] ]);
        $this->model->save();

        $this->card = $card;

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
     * Dynamically check a values existence from the creditcard.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->info[$key]);
    }

    /**
     * Dynamically get values from the creditcard.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->info[$key]) ? $this->info[$key] : null;
    }

}
