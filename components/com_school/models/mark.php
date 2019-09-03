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
class SchoolModelMark extends JModelItem
{
	public function getItem()
	{		
		$app=JFactory::getApplication();
		$id=$app->input->get("id");


		$db = JFactory::getDbo();
		//print_r($db);
		$query = $db->getQuery(true);
		$query->select('*')
			 ->from('#__mark')
			 ->where('id=' . (int)$id);
		$db->setQuery((string)$query);
		if ($this->item = $db->loadObject()) 
		{
			 
		
			return $this->item;
		}
			
	}
} 