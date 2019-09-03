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

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

// Include dependancies
jimport('joomla.application.component.controller');

// Include hepler
$helperPath = JPATH_SITE . '/components/com_jmailalerts/helpers/emailhelper.php';

if (!class_exists('jmailalertsemailhelper'))
{
	JLoader::register('jmailalertsemailhelper', $helperPath);
	JLoader::load('jmailalertsemailhelper');
}

// Load assets
jimport('joomla.filesystem.file');
$tjStrapperPath = JPATH_ROOT . '/media/techjoomla_strapper/tjstrapper.php';

if (JFile::exists($tjStrapperPath))
{
	require_once $tjStrapperPath;
	TjStrapper::loadTjAssets('com_jmailalerts');
}

// Define constants
if (JVERSION < '3.0')
{
	// Define wrapper class
	define('JMAILALERTS_WRAPPER_CLASS', "jmailalerts-wrapper techjoomla-bootstrap");
}
else
{
	// Define wrapper class
	define('JMAILALERTS_WRAPPER_CLASS', "jmailalerts-wrapper");
}

// Execute the task.
$controller = JControllerLegacy::getInstance('Jmailalerts');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
