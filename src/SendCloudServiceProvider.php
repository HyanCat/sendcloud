<?php

namespace Hyancat\Sendcloud;

use Illuminate\Support\ServiceProvider;

class SendCloudServiceProvider extends ServiceProvider
{
	protected $defer = true;

	public function register()
	{
		$this->app->singleton('sendcloud', function ($app) {
			return new SendCloudPusher();
		});
	}

	public function provides()
	{
		return ['sendcloud'];
	}


}