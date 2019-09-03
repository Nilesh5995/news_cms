<?php
/**
 * @version    SVN: <svn_id>
 * @package    JMailAlerts
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jmailalerts'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

// Load assets
jimport('joomla.filesystem.file');
$tjStrapperPath = JPATH_ROOT . '/media/techjoomla_strapper/tjstrapper.php';

if (JFile::exists($tjStrapperPath))
{
	require_once $tjStrapperPath;
	TjStrapper::loadTjAssets('com_jmailalerts');
}

$document = JFactory::getDocument();
$document->addStyleSheet(Juri::base() . 'components/com_jmailalerts/assets/css/jmailalerts.css');

// Define constants
if (JVERSION < '3.0')
{
	// Define wrapper class
	define('JMAILALERTS_WRAPPER_CLASS', "jmailalerts-wrapper techjoomla-bootstrap");

	// Other
	JHtml::_('behavior.tooltip');
}
else
{
	// Define wrapper class
	define('JMAILALERTS_WRAPPER_CLASS', "jmailalerts-wrapper");

	// Tabstate
	JHtml::_('behavior.tabstate');

	// Other
	JHtml::_('behavior.tooltip');

	// Bootstrap tooltip and chosen js
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('formbehavior.chosen', 'select');
}

// Load manage user helper
$manageUserHelperPath = JPATH_ADMINISTRATOR . '/components/com_jmailalerts/helpers/manageuser.php';

if (!class_exists('ManageUserHelper'))
{
	JLoader::register('ManageUserHelper', $manageUserHelperPath);
	JLoader::load('ManageUserHelper');
}

// Include dependancies
jimport('joomla.application.component.controller');
$controller = JControllerLegacy::getInstance('Jmailalerts');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
