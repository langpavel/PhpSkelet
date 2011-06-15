<?php

/**
 * View interface, merging IProvidePermalink and IResponse
 *
 * @author Pavel Lang (langpavel@phpskelet.org)
 * @see IResponse
 * @see IProvidePermalink
 */
interface IView extends IResponse, IProvidePermalink
{
}
