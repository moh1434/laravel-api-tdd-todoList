<?php

namespace App\Providers;

use Google\Client;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function () {
            $client = new Client();

            $config = config('services.google');
            $client->setClientId($config['id']);
            $client->setClientSecret($config['secret']);
            $client->setRedirectUri($config['redirect_url']);

            return $client;
        });

        $this->app->singleton(TwilioClient::class, function () {
            $config = config('services.twilio');

            $client = new TwilioClient($config['account_sid'], $config['auth_token']);

            return $client;
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
