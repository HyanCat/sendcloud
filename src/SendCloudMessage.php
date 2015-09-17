<?php

namespace Hyancat\Sendcloud;


class SendCloudMessage
{
	protected $from = [];
	protected $subject;
	protected $body;
	protected $to = [];
	protected $maillist = [];

	public function getAddr()
	{
		return array_key_exists('addr', $this->from) ? $this->from['addr'] : null;
	}

	public function getName()
	{
		return array_key_exists('name', $this->from) ? $this->from['name'] : null;
	}

	public function from($addr, $name)
	{
		$this->from = ['addr' => $addr, 'name' => $name];

		return $this;
	}

	public function body($body = null)
	{
		if (is_null($body)) {
			return $this->body;
		}
		$this->body = $body;

		return $this;
	}

	public function to(array $to = null)
	{
		if (is_null($to)) {
			return $this->to;
		}
		$this->to = $to;

		return $this;
	}

	public function maillist(array $maillist = null)
	{
		if (is_null($maillist)) {
			return $this->maillist;
		}
		$this->maillist = $maillist;

		return $this;
	}

	public function subject($subject = null)
	{
		if (is_null($subject)) {
			return $this->subject;
		}
		$this->subject = $subject;

		return $this;
	}
}