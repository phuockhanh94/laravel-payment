<?php

namespace GGPHP\Payment\CustomerTrait;

use Illuminate\Database\Eloquent\Model;
use GGPHP\Payment\Gateways\ChargeInterface;
use GGPHP\Payment\Facades\Payment as Billing;

class Charge
{
    /**
     * Customer model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Charge gateway instance.
     *
     * @var GGPHP\Payment\Gateways\ChargeInterface
     */
    protected $charge;

    /**
     * Charge info array.
     *
     * @var array
     */
    protected $info;

    /**
     * Local copy of the invoice object for this charge.
     *
     * @var Invoice
     */
    protected $invoice;

    /**
     * The ard token
     *
     * @var string
     */
    protected $cardToken;

    /**
     * The ard token
     *
     * @var string
     */
    protected $card;

    /**
     * Create a new CustomerBillableTrait Charge instance.
     *
     * @param \Illuminate\Database\Eloquent\Model      $model
     * @param GGPHP\Payment\Gateways\ChargeInterface $charge
     *
     * @return void
     */
    public function __construct(Model $model, ChargeInterface $charge = null, $info = [])
    {
        $this->model = $model;
        $this->charge = $charge;
        $this->info = $info;
    }

    /**
     * Fetch the charge.
     *
     * @return array
     */
    public function all()
    {
        $charges = [];

        if (!$customer = $this->model->gatewayCustomer()) {
            return;
        }

        foreach ($customer->charge()->all() as $charge) {
            $charges[] = new Charge(
                $this->model,
                $charge,
                $charge->info()
            );
        }

        return $charges;
    }

    /**
     * Get first charge.
     *
     * @return Charge
     */
    public function first()
    {
        $charges = $this->all();

        return empty($charges) ? [] : $charges[0];
    }

    /**
     * Gets info for a card.
     *
     * @return array|null
     */
    public function info()
    {

    }

    /**
     * Create a new card.
     *
     * @param string $card_token
     *
     * @return CardInterface
     */
    public function create($amount, $properties = [])
    {
        // Check if not exist customer, create customer
        if (!$customer = $this->model->gatewayCustomer()) {
            $this->model->payment()->create($properties);
        }

        if (!$this->card) {
            if (!$this->cardToken && empty($this->model->payment_cards)) {
                return null;
            }
            if ($this->cardToken) {
                $this->card = $this->model->card()->create($this->cardToken)->id;
            }
        }

        $properties = $this->card ? array_merge($properties, ['card' => $this->card]) : $properties;
        $charge = Billing::charge(null, $customer)->create($amount, array_merge($properties, [
            'card' => $this->card,
        ]));

        $info = $charge->info();

        $this->charge = $charge;
        $this->info = $info;

        return $this;
    }

    /**
     * Update the charge
     *
     * @param array $properties
     *
     * @return Charge
     */
    public function update($properties = [])
    {
        if (!$this->model->gatewayCustomer()) {
            return;
        }

        $this->charge->update($properties);
        $this->info = $this->charge->info();

        return $this;
    }

    /**
     * Capture charge
     *
     * @param array $properties
     *
     * @return Charge
     */
    public function capture(array $properties = [])
    {
        if (!$this->model->gatewayCustomer()) {
            return;
        }

        $this->charge->capture($properties);
        $this->info = $this->charge->info();

        return $this;
    }

     /**
     * Refund charge
     *
     * @param array $properties
     *
     * @return Charge
     */
    public function refund($properties = [])
    {
        if (!$this->model->gatewayCustomer()) {
            return;
        }

        $this->charge->refund($properties);
        $this->info = $this->charge->info();

        return $this;
    }

    /**
     * Get card token
     *
     * @param string $cardToken
     *
     * @return Charges
     */
    public function withCardToken($cardToken)
    {
        $this->cardToken = $cardToken;

        return $this;
    }

    /**
     * Get card token
     *
     * @param string $cardToken
     *
     * @return Charges
     */
    public function withCard($card)
    {
        $this->card = $card;

        return $this;
    }
}
