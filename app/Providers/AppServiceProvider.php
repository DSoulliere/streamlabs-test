<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Braintree\Gateway;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Gateway::class, function ($app) {
            return new Gateway([
                'environment' => config('services.braintree.environment'),
                'merchantId' => config("services.braintree.merchant_id"),
                'publicKey' => config("services.braintree.public_key"),
                'privateKey' => config("services.braintree.private_key")
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
