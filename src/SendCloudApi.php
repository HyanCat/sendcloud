<?php

namespace Hyancat\Sendcloud;


use Curl\Curl;
use Illuminate\Contracts\Config\Repository;

class SendCloudApi implements SendCloudApiInterface
{
	const API_INVALID_STAT = 'http://sendcloud.sohu.com/webapi/invalidStat.get.json';
	const API_BOUNCES = 'http://sendcloud.sohu.com/webapi/bounces.get.json';
	const API_BOUNCES_DELETE = 'http://sendcloud.sohu.com/webapi/bounces.delete.json';

	protected $config;

	private $apiUser;
	private $apiKey;

	public function __construct(Repository $config)
	{
		$this->config  = $config;
		$this->apiUser = $this->config->get('sendcloud.api.user');
		$this->apiKey  = $this->config->get('sendcloud.api.key');
	}

	public function invalidStat($start, $end)
	{
		$param = [
			'api_user'   => $this->apiUser,
			'api_key'    => $this->apiKey,
			'start_date' => $start,
			'end_date'   => $end,
		];

		$curl     = new Curl();
		$response = $curl->get(self::API_INVALID_STAT, $param);
		dd($curl);

		return $response;
	}

	public function bounces($start, $end, $limit = 100)
	{
		$param = [
			'api_user'   => $this->apiUser,
			'api_key'    => $this->apiKey,
			'start_date' => $start,
			'end_date'   => $end,
			'start'      => 0,
			'limit'      => $limit,
		];

		$curl     = new Curl();
		$response = $curl->get(self::API_BOUNCES, $param);

		return $response;
	}

	public function deleteBounce($email)
	{
		$param = [
			'api_user' => $this->apiUser,
			'api_key'  => $this->apiKey,
			'email'    => $email,
		];

		$curl     = new Curl();
		$response = $curl->get(self::API_BOUNCES_DELETE, $param);

		return $response;
	}


}