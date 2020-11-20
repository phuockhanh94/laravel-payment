<?php

namespace GGPHP\Payment\CustomerTrait;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use GGPHP\Payment\Gateways\CardInterface;

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
     * @var \GGPHP\Payment\Gateways\CardInterface
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
     * @param \GGPHP\Payment\Gateways\CardInterface $card
     * @param array                                  $info
     *
     * @return void
     */
    public function __construct(Model $model, CardInterface $card = null, $info = [])
    {
        $this->model = $model;
        $this->card = $card;
        $this->info = $info;
    }

    /**
     * Get all the credit cards.
     *
     * @return array
     */
    public function all()
    {
        $cards = [];

        if (!$customer = $this->model->gatewayCustomer()) {
            return;
        }

        foreach ($this->model->payment_cards as $cardId) {
            $cards[] = new Card(
                $this->model,
                $customer->card($cardId),
                $customer->card($cardId)->info()
            );
        }

        return $cards;
    }

    /**
     * Get first card.
     *
     * @return Creditcard
     */
    public function first()
    {
        if (!$customer = $this->model->gatewayCustomer()) {
            return;
        }

        if (empty($this->model->payment_cards)) {
            return null;
        }

        return new Card(
            $this->model,
            $customer->card($this->model->payment_cards[0]),
            $customer->card($this->model->payment_cards[0])->info()
        );
    }

    /**
     * Create the card
     *
     * @param  mixed $properties
     * @return Payment
     */
    public function create($cardToken)
    {
        if (!$customer = $this->model->gatewayCustomer()) {
            return;
        }
        $card = $customer->card()->create($cardToken);

        $this->model->payment_cards = array_merge($this->model->payment_cards, [ $card->getId() ]);
        $this->model->save();

        $this->info = $card->info();

        $this->card = $card;

        return $this;
    }

    /**
     * Update the card
     *
     * @param array $properties
     *
     * @return Card
     */
    public function update($properties = [])
    {
        if (!$this->model->gatewayCustomer()) {
            return $this;
        }

        $this->card->update($properties);
        $this->info = $this->card->info();

        return $this;
    }

    /**
     * Delete the card
     *
     * @return Card
     */
    public function delete()
    {
        if (!$this->model->gatewayCustomer()) {
            return $this;
        }

        $this->card->delete();

        foreach ($cards = $this->model->payment_cards as $key => $cardId) {
            if ($cardId == $this->id) {
                unset($cards[$key]);
                break;
            }
        }
        $this->model->payment_cards = $cards;
        $this->model->save();

        $this->info = ['id' => $this->id];

        return $this;
    }

    /**
     * Find Card.
     *
     * @param mixed $id
     *
     * @return Card
     */
    public function find($id)
    {
        if (!$customer = $this->model->gatewayCustomer()) {
            return;
        }

        if (empty($this->model->payment_cards)) {
            return null;
        }

        foreach ($this->model->payment_cards as $cardId) {
            if ($id == $cardId) {
                return new Card(
                    $this->model,
                    $customer->card($this->model->payment_cards[0]),
                    $customer->card($this->model->payment_cards[0])->info()
                );
            }
        }

        return null;
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
     * Dynamically get values from the card.
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
