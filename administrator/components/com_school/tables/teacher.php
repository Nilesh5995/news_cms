<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.database.table' );

/**
 * Hello Table class
 *
 * @since  0.0.1
 */
class SchoolTableTeacher extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__teacher', 'id', $db);
	}
	public function bind($array, $ignore = '')
	{
		if (isset($array['imageinfo']) && is_array($array['imageinfo']))
		{
			// Convert the imageinfo array to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['imageinfo']);
			$array['image'] = (string)$parameter;

			// die();
		}
		return parent::bind($array, $ignore);
	}
	// function remove($pk = null)
	// {
	// 	$this->load($pk);
	// 	parent::delete($pk);
	// }
}