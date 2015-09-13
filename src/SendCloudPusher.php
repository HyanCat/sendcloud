<?php

namespace Hyancat\Sendcloud;


use Curl\Curl;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;

class SendCloudPusher implements SendCloudInterface
{
	const API_MAIL_SEND = 'http://sendcloud.sohu.com/webapi/mail.send.json';
	const API_MAIL_SEND_TEMPLATE = 'http://sendcloud.sohu.com/webapi/mail.send_template.json';

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
		$message->body($content);

		if ($callback instanceof \Closure) {
			call_user_func($callback, $message);
		}

		return $this->push($message);
	}

	public function sendTemplate($template, $data, $callback)
	{
		$message = new SendCloudMessage();
		if ($callback instanceof \Closure) {
			call_user_func($callback, $message);
		}

		return $this->pushWithTemplate($message, $template, $data);
	}

	protected function push(SendCloudMessage $message)
	{
		$param    = $this->buildParamWithMessage($message, ['html' => $message->body()]);
		$response = (new Curl)->post(self::API_MAIL_SEND, $param);

		return $response;
	}

	protected function pushWithTemplate(SendCloudMessage $message, $template, array $data)
	{
		if (empty($data)) {
			$param = $this->buildParamWithMessage($message, [
				'use_maillist'         => 'true',
				'template_invoke_name' => $template,
			]);
		}
		else {
			$param = $this->buildParamWithMessage($message, [
				'use_maillist'         => false,
				'substitution_vars'    => json_encode([
					'to'  => $message->to(),
					'sub' => $data,
				]),
				'template_invoke_name' => $template,
			]);
		}

		$response = (new Curl)->post(self::API_MAIL_SEND_TEMPLATE, $param);

		return $response;
	}

	private function buildParamWithMessage(SendCloudMessage $message, array $appendParam = [])
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
		];
		$param    = array_merge($param, $appendParam);

		return $param;
	}
}