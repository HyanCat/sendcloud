<?php

namespace Hyancat\Sendcloud;


interface SendCloudApiInterface
{
	public function invalidStat($start, $end);

	public function bounces($start, $end, $limit = 100);

	public function deleteBounce($email);
}