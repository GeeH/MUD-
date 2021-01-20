<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;
use Twilio\Rest\Sync\V1\ServiceContext;

class SyncServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            ServiceContext::class,
            function (): ServiceContext {
                $client = new Client(config('services.twilio.account'), config('services.twilio.token'));
                return $client->sync->v1->services(config('services.twilio.sync_service'));
            }
        );
    }

    public function boot()
    {
        //
    }
}
