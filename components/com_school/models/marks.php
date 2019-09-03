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
class SchoolModelMarks extends JModelList
{

	protected function getListQuery()
	{

		$db = Jfactory::getDbo();
		$query = $db->getQuery(true);
		// Create the base select statement.
		 $query->select('*')
                ->from($db->quoteName('#__mark'));

		return $query;

	}
	
}