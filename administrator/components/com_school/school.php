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

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('School');
// Access check: is this user allowed to access the backend of this component?
// Access check: is this user allowed to access the backend of this component?
$abc = JFactory::getUser()->authorise('student.manage', 'com_school');
if (!JFactory::getUser()->authorise('student.manage', 'com_school'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();