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
class HelloWorldModelAjaxdata extends JModelItem
{
	protected $item;
	protected $id;
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getData($id)
	{		
			// $db    = JFactory::getDbo();
			// $query = $db->getQuery(true);
			// $query->select('greeting, params,  email as email, mobile as mobile, image as image, c.title as category, latitude as latitude, longitude as longitude')
			// 	  ->from('#__helloworld as h')
			// 	  ->where('id=' . (int)$id);
			// 	 $db->setQuery((string)$query);
			// 	$results = $db->loadObjectItem();
			// 	var_dump($results->greeting);
		//$id    = $this->getState('message.id');
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('greeting, params ,email,mobile ,image , latitude ,longitude')
				  ->from('#__helloworld')
				  
				 ->where('id=' . (int)$id);
			$db->setQuery((string)$query);
		
			if ($this->item = $db->loadObject()) 
			{
				// Load the JSON string
				//print_r($this->item->greeting);
				return $this->item;
			}
			
	}
	
}