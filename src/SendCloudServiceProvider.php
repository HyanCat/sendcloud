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
		$this->app->bind('Hyancat\Sendcloud\SendCloudInterface', 'Hyancat\Sendcloud\SendCloudPusher');
		$this->app->bind('Hyancat\Sendcloud\SendCloudApiInterface', 'Hyancat\Sendcloud\SendCloudApi');
		$this->app->singleton('sendcloud', function ($app) {
			return $app->make('Hyancat\Sendcloud\SendCloudInterface');
		});
	}

	public function provides()
	{
		return ['sendcloud'];
	}


}