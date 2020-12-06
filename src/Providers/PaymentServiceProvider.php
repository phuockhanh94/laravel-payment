<?php

namespace GGPHP\Payment\Providers;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();

        $this->loadMigrationsFrom(__DIR__ . '/../Databases/migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        $this->app->bind('payment.gateway', function ($app) {
            switch (config('payment.default')) {
                case 'stripe':
                    return new \GGPHP\Payment\Gateways\Stripe\Gateway;
                case 'paypal':
                    return new \GGPHP\Payment\Gateways\Paypal\Gateway;

                default:
                    return null;
            }
        });
    }

    private function mergeConfig()
    {
        $path = $this->getConfigPath();
        $this->mergeConfigFrom($path, 'payment');
    }

    private function publishConfig()
    {
        $path = $this->getConfigPath();
        $this->publishes([$path => config_path('payment.php')], 'payment-config');
    }

    private function getConfigPath()
    {
        return __DIR__ . '/../Config/payment.php';
    }
}
