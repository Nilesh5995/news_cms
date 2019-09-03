<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;
	/**
	 * Get a list of articles from a specific category
	 *
	 * @param   \Joomla\Registry\Registry  $user  object holding the user parameters
	 *
	 * @return  mixed
	 *
	 * @since  1.6
	 */
class PlgUserUser_Logout extends JPlugin
{
	protected $app;

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data
	 * @param   array  $options  Array holding options (remember, autoregister, group)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.5
	 */
	public function onUserLogin($user, $options = array())
	{
		$name = $user['username'];
		$sitename = JFactory::getApplication()->get('sitename');

		JFactory::getApplication()->enqueueMessage(JText::_('hi ' . $name . '  welcome to ' . $sitename), 'notice');
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data
	 * @param   array  $options  Array holding options (remember, autoregister, group)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.5
	 */
	public function onUserLogout($user, $options = array())
	{
		$session = JFactory::getSession();
		$user    = JFactory::getUser();
		$session->clear('user');
		$app = JFactory::getApplication();
		$message = JText::sprintf('Bye bye');
		$app->redirect(JRoute::_('index.php', false), $message, 'warning');

		return true;
	}
}
