<?php

/**
 * For all objects that can be accessed via URI
 *
 * @author Pavel Lang (langpavel@phpskelet.org)
 */
interface IProvidePermalink
{
	/**
	 * Get URL of current object
	 * @return string|URI can return both, string or instance of URI. String is preffered
	 */
	public function getPermalink();
}
