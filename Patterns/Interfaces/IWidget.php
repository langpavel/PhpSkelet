<?php

/**
 *
 * This file is part of the Morpheus Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the Morpheus/LGPL license.
 */

interface IWidget extends IComposite
{
	public function renderOutput(IOutputProvider $output);	
}
