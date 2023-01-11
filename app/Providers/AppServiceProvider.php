<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Braintree\Gateway;
use App\Http\Services\BraintreeService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BraintreeService::class, function ($app) {
            return new BraintreeService([
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
