<?php

/**
 * Interface for HTTP response objects.
 *
 * @author Pavel Lang (langpavel@phpskelet.org)
 */
interface IResponse
{
	/**
	 * write appropriate HTTP headers
	 */
	public function writeResponseHeader();

	/**
	 * write whole response content
	 */
	public function writeResponseContent();

	/**
	 * subsequent call of writeResponseHeader() and writeResponseContent()
	 */
	public function writeResponse();
}
