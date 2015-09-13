<?php

namespace Hyancat\Sendcloud;


interface SendCloudInterface
{
	/**
	 * Send email with blade view.
	 * @param $view     The blade view name.
	 * @param $data     The data for view.
	 * @param $callback a closure to make a SendCloudMessage.
	 * @return mixed
	 */
	public function send($view, $data, $callback);

	/**
	 * Send email with blade view.
	 * @param $template     The sendcloud template name.
	 * @param $data         The data for template.
	 * @param $callback     a closure to make a SendCloudMessage.
	 * @return mixed
	 */
	public function sendTemplate($template, $data, $callback);
}