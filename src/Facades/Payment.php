<?php

namespace GGPHP\Payment\Facades;

class Payment extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'payment.gateway';
    }
}
