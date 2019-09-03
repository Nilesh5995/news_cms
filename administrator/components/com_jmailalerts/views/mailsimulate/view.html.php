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

class jmailalertsViewmailsimulate extends JViewLegacy
{
	function display($tpl = null)
	{
		//Get the model
		$model = $this->getModel();
		//get alerttype
		$alertname=$model->getAlertypename();
		$this->assignRef('alertname',$alertname);

		JmailalertsHelper::addSubmenu('mailsimulate');

		$this->_setToolBar();
		if(JVERSION>=3.0)
			$this->sidebar = JHtmlSidebar::render();

		$this->setLayout('mailsimulate');
		parent::display();
	}

	function _setToolBar()
	{
		JToolBarHelper::preferences('com_jmailalerts');

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_MAILSIMULATE'), 'envelope');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_MAILSIMULATE'), 'sync.png');
		}
	}
}
