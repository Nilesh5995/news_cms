<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */

class SchoolModelSchool extends JModelItem
{
	/**
	 * @var string message
	 */
	protected $message;
	public function getMsg()
	{
		if (!isset($this->message))
		{
			$this->message = 'this is the student management system';
		}

		return $this->message;
	}
}