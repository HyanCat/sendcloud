<?php

namespace Hyancat\Sendcloud;

use Illuminate\Support\Facades\Facade;

class SendCloudFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'sendcloud';
	}

}