<?php
/**
 * This file is part of HyanCat/SendCloud.
 *
 * Created by HyanCat.
 *
 * Copyright (C) HyanCat. All rights reserved.
 */
namespace HyanCat\SendCloud;

use Illuminate\Mail\TransportManager;
use Illuminate\Support\ServiceProvider;

/**
 * A service provider that make a extend driver named "sendcloud" for sending email.
 * Class SendCloudServiceProvider
 * @namespace HyanCat\SendCloud
 */
class SendCloudServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sendcloud.php' => config_path('sendcloud.php'),
        ]);
    }

    public function register()
    {
        // bind interface.
        $this->app->bind(SendCloudInterface::class, SendCloudMailer::class);

        $this->registerTransport();
    }

    protected function registerTransport()
    {
        // extend transport.
        $this->app->resolving('swift.transport', function (TransportManager $transportManager) {
            $transportManager->extend('sendcloud', function ($app) {
                return new SendCloudTransport($app[SendCloudInterface::class]);
            });
        });
    }
}
