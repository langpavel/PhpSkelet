<?php

final class SendMail extends SafeObject
{
	private $headers = array();
	private $message;

	public function __construct()
	{
		parent::__construct();
	}

	public function setHeader($key, $value)
	{
		$this->headers[$key] = $value;
	}

	public function setContent($message)
	{
		$this->message = $message;
	}

	public function send()
	{
		$headers = array();
		foreach($this->headers as $key=>$header)
		{
			if($key == 'To' || $key == 'Subject')
				continue;

			// TODO: Escape headers
			if(is_numeric($key))
				$headers[] = $header;
			else
				$headers[] = "$key: $header";
		}

		return mail($this->headers['To'],
			$this->headers['Subject'],
			$this->message, implode(PHP_EOL, $headers));
	}
}