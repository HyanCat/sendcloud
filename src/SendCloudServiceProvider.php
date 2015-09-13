<?php

namespace Hyancat\Sendcloud;

use Illuminate\Support\ServiceProvider;

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
		$this->app->singleton('sendcloud', function ($app) {
			return $app->make('Hyancat\Sendcloud\SendCloudPusher');
		});
	}

	public function provides()
	{
		return ['sendcloud'];
	}


}