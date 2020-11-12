<?php

namespace GGPHP\Payment\Gateways;

interface CardInterface
{
    /**
     * Gets info for a card.
     *
     * @return array|null
     */
    public function info();

    /**
     * Create a new card.
     *
     * @param string $card_token
     *
     * @return CardInterface
     */
    public function create($cardToken);

    /**
     * Update a card.
     *
     * @param array $properties
     *
     * @return CardInterface
     */
    public function update(array $properties = array());

    /**
     * Delete a card.
     *
     * @return CardInterface
     */
    public function delete();
}
