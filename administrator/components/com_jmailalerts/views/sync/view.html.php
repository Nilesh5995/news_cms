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

jimport('joomla.application.component.view');

class jmailalertsViewsync extends JViewLegacy
{
	function display($tpl = null)
	{
		// Get the model
		$model=$this->getModel();

		// Get alert name
		$alertname = $model->getAlertnames();
		$this->assignRef('alertname',$alertname);

		// Get enables plugin names and element
		$plugin_data=$model->getPluginData();
		$this->assignRef('plugin_data',$plugin_data);

		// Get the plugin names under email-alerts
		$email_alert_plugin_names = $model->getPluginNames();

		// Assign a ref	to the array
		$this->assignRef('email_alert_plugin_names', $email_alert_plugin_names);

		JmailalertsHelper::addSubmenu('sync');
		if(JVERSION>=3.0)
			$this->sidebar = JHtmlSidebar::render();

		$this->_setToolBar();

		parent::display();
	}

	function _setToolBar()
	{
		// Get the toolbar object instance
		$bar =JToolBar::getInstance('toolbar');

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_SYNC'), 'users');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_SYNC'), 'sync.png');
		}

		JToolBarHelper::preferences('com_jmailalerts');
	}
}
