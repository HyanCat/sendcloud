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

	private $error;
	private $response;

	public function __construct(Repository $config, Factory $views)
	{
		$this->config = $config;
		$this->views  = $views;
	}

	public function send($view, array $data, \Closure $callback)
	{
		$message = new SendCloudMessage();
		$content = $this->views->make($view, $data)->render();
		$message->body($content);

		if ($callback instanceof \Closure) {
			call_user_func($callback, $message);
		}

		return $this->push($message);
	}

	public function sendTemplate($template, array $data, \Closure $callback)
	{
		$message = new SendCloudMessage();
		if ($callback instanceof \Closure) {
			call_user_func($callback, $message);
		}

		return $this->pushWithTemplate($message, $template, $data);
	}

	public function success(\Closure $callback)
	{
		if ($this->error->code === 0 && is_callable($callback)) {
			call_user_func($callback, $this->response);
		}

		return $this;
	}

	public function failure(\Closure $callback)
	{
		if ($this->error->code != 0 && is_callable($callback)) {
			call_user_func($callback, $this->response, $this->error);
		}

		return $this;
	}

	// 推送普通邮件
	protected function push(SendCloudMessage $message)
	{
		// 构造参数
		// 如果 use_maillist = true, to = merge(收件人, maillist)
		$param = $this->buildParamWithMessage($message, [
			'html' => $message->body(),
			'to'   => implode(';', array_merge($message->to(), $message->maillist())),
		]);

		$curl = new Curl;
		// 发送回调
		$curl->complete(function (Curl $instance) {
			$this->checkError($instance);
		});
		// 发送
		$response = $curl->post(self::API_MAIL_SEND, $param);

		return $this;
	}

	// 推送模板邮件
	protected function pushWithTemplate(SendCloudMessage $message, $template, array $data)
	{
		// 构造参数
		// 如果 不使用 maillist
		if (empty($message->maillist())) {
			if (empty($data)) {
				// 不需要模板数据, 构造无用填充数据
				$data = ['nothing' => $message->to()];
			}
			$param = $this->buildParamWithMessage($message, [
				'use_maillist'         => 'false',
				'substitution_vars'    => json_encode([
					'to'  => $message->to(),
					'sub' => $data,
				]),
				'template_invoke_name' => $template,
			]);
		}
		// 使用 maillist
		else {
			$param = $this->buildParamWithMessage($message, [
				'use_maillist'         => 'true',
				'to'                   => implode(';', $message->maillist()),
				'template_invoke_name' => $template,
			]);
		}

		$curl = new Curl;
		// 发送回调
		$curl->complete(function (Curl $instance) {
			$this->checkError($instance);
		});
		// 发送
		$curl->post(self::API_MAIL_SEND_TEMPLATE, $param);

		return $this;
	}

	private function checkError(Curl $curl)
	{
		$this->response = $curl->response;
		// 如果请求失败
		if ($curl->error) {
			$this->makeError($curl->errorCode, $curl->errorMessage);

			return;
		}

		// 成功
		if (property_exists($this->response, 'message') && $this->response->message === 'success') {
			$this->makeError(0, 'success');

			return;
		}
		// 返回结果不成功
		$message = '';
		if (property_exists($this->response, 'errors')) {
			$this->response->errors = is_array($this->response->errors) ? implode(';', $this->response->errors) : $this->response->errors;
			$message                = is_string($this->response->errors) ? $this->response->errors : '';
		}
		$this->makeError(1001, $message);

		return;
	}

	private function makeError($code, $message)
	{
		$error          = new \stdClass();
		$error->code    = $code;
		$error->message = $message;

		$this->error = $error;
	}

	private function buildParamWithMessage(SendCloudMessage $message, array $appendParam = [])
	{
		$api_user = $this->config->get('sendcloud.api.user');
		$api_key  = $this->config->get('sendcloud.api.key');
		$from     = $this->config->get('sendcloud.from.address');
		$fromname = $this->config->get('sendcloud.from.name');
		$param    = [
			'api_user'      => $api_user,
			'api_key'       => $api_key,
			'from'          => $message->getAddr() ?: $from,
			'fromname'      => $message->getName() ?: $fromname,
			'subject'       => $message->subject(),
			'resp_email_id' => 'true',
		];
		$param    = array_merge($param, $appendParam);

		return $param;
	}
}