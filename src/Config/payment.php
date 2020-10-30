<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Billing Gateway Driver
    |--------------------------------------------------------------------------
    |
    | The Billing API supports a variety of gateways via an unified
    | API, giving you convenient access to each gateway using the same
    | syntax for each one. Here you may set the default billing gateway driver.
    |
    | Supported: "stripe", "paypal"
    |
    */

    'default' => 'stripe',

    /*
    |--------------------------------------------------------------------------
    | Customer Tables
    |--------------------------------------------------------------------------
    |
    | Define table payment customer
    |
    */

    'customer_table' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Customer Models
    |--------------------------------------------------------------------------
    |
    | Define all of the model classes that act as a billing customer.
    |
    */

    'customer_models' => ['User'],

    /*
    |--------------------------------------------------------------------------
    | Subscription Models
    |--------------------------------------------------------------------------
    |
    | Define all of the model classes that act as a billing subscription.
    |
    */

    'subscription_models' => ['User'],

    /*
    |--------------------------------------------------------------------------
    | Gateway Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for the gateway that
    | is used by your application. A default configuration has been added
    | for each gateway shipped with Billing. You are free to add more.
    |
	*/

    'gateways' => [
        'stripe' => [
            'public_test_key'  => '',
            'secrect_test_key' => '',
            'public_key'       => '',
            'secrect_key'      => '',
            'test_mode'        => true
        ],

        'paypal' => [
            'public_test_key'  => '',
            'secrect_test_key' => '',
            'public_key'       => '',
            'secrect_key'      => '',
            'test_mode'        => true,
        ],
    ],
);
