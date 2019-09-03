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
class SchoolModelTeacher Extends JModelItem
{
	public function getTable($type= 'Teacher', $prefix = 'SchoolTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);

	}

	public function getItem()
	{
		$app=JFactory::getApplication();
		$id=$app->input->get("id");
		$db = JFactory::getDbo();
		//print_r($db);
		$query = $db->getQuery(true);
		$query->select('*')
			 ->from('#__teacher')
			 ->where('id=' . (int)$id);
		$db->setQuery((string)$query);
		if ($this->item = $db->loadObject()) 
		{
			// Merge global params with item params
			// Convert the JSON-encoded image info into an array
			$image = new JRegistry;
			$image->loadString($this->item->image, 'JSON');
			$this->item->imageDetails = $image;
		

			return $this->item;
		}
	}
	public function delete($pk)
	{
		foreach($pk as $pks)
		{
			$table = $this->getTable();
			$table = $table->delete($pks);
		}
		
		return true;
	}
}