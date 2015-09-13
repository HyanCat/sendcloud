<?php

namespace Hyancat\Sendcloud;


use Curl\Curl;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;

class SendCloudPusher
{
	const API_MAIL_SEND = 'http://sendcloud.sohu.com/webapi/mail.send.json';

	protected $config;
	protected $views;

	public function __construct(Repository $config, Factory $views)
	{
		$this->config = $config;
		$this->views  = $views;
	}

	public function send($view, $data, $callback)
	{
		$message = new SendCloudMessage();
		$content = $this->views->make($view, $data)->render();
		$this->buildMessage($message, $content);

		if ($callback instanceof \Closure) {
			call_user_func($callback, $message);
		}

		return $this->push($message);
	}

	protected function push(SendCloudMessage $message)
	{
		$api_user = $this->config->get('sendcloud.api.user');
		$api_key  = $this->config->get('sendcloud.api.key');
		$from     = $this->config->get('sendcloud.from.address');
		$fromname = $this->config->get('sendcloud.from.name');
		$param    = [
			'api_user' => $api_user,
			'api_key'  => $api_key,
			'from'     => $message->getAddr() ?: $from,
			'fromname' => $message->getName() ?: $fromname,
			'to'       => implode(';', $message->to()),
			'subject'  => $message->subject(),
			'html'     => $message->body(),
		];

		$curl     = new Curl;
		$response = $curl->post(self::API_MAIL_SEND, $param);

		return $response;
	}

	private function buildMessage(SendCloudMessage $message, $body)
	{
		$message->body($body);
	}
}