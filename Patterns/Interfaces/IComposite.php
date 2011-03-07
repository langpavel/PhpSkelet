<?php

/**
 *
 * This file is part of the Morpheus Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the Morpheus/LGPL license.
 */

interface IComposite extends ArrayAccess, IteratorAggregate
{
	public function getName();
	
	public function hasChilds();
	public function getChilds();
	
	public function addChild(IComposite $composite);
	public function removeChild(IComposite $composite);
}