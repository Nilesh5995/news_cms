
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
 * HelloWorld View
 *
 * @since  0.0.1
 */
class SchoolViewTeacher Extends JViewlegacy
{
	public function display($tpl = null)
	{
		$this->item = $this->get('Item');
		// if (!($this->canDo->get('teacher.delete'))) 
		// {
		// 	$app = JFactory::getApplication(); 
		// 	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		// 	$app->setHeader('status', 403, true);
		// 	return;
		// }
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}
		// Display the template
		parent::display($tpl);
	}
}
