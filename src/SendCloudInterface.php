<?php

namespace Hyancat\Sendcloud;


interface SendCloudInterface
{
	/**
	 * Send email with blade view.
	 * @param  string  $view     The blade view name.
	 * @param array    $data     The data for view.
	 * @param \Closure $callback a closure to make a SendCloudMessage.
	 * @return self
	 */
	public function send($view, array $data, \Closure $callback);

	/**
	 * Send email with blade view.
	 * @param string   $template The sendcloud template name.
	 * @param array    $data     The data for template.
	 * @param \Closure $callback a closure to make a SendCloudMessage.
	 * @return self
	 */
	public function sendTemplate($template, array $data, \Closure $callback);

	/**
	 * Callback of Success.
	 * @param \Closure $callback
	 * @return self
	 */
	public function success(\Closure $callback);

	/**
	 * Callback of Success.
	 * @param \Closure $callback
	 * @return self
	 */
	public function failure(\Closure $callback);
}