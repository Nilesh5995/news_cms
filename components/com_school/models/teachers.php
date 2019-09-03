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
class SchoolModelTeachers Extends JModelList
{
	protected function getListQuery()
	{
		$db = Jfactory::getDbo();
		$query = $db->getQuery(true);
		// Create the base select statement.
		$query->select('*')
                ->from($db->quoteName('#__teacher'));
		return $query;
	}
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(
			'com_school.schoolform',
			'studentform',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			$errors = $this->getErrors();
			throw new Exception(implode("\n", $errors), 500);
			//echo "error";
		}
		//print_r($form);
		//die();
		return $form;
	}
	
	public function getTable($type= 'Teachers', $prefix = 'SchoolTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);

	}
}