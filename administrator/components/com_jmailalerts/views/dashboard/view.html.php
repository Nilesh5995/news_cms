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

/**
 * View class for JMailAlerts Dashboard.
 *
 * @package  JMailAlerts
 *
 * @since    2.5
 */
class JmailalertsViewDashboard extends JViewLegacy
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get download id
		$params = JComponentHelper::getParams('com_jmailalerts');
		$this->downloadid = $params->get('downloadid');

		// Get model
		$model = $this->getModel();

		// Refresh update site
		$model->refreshUpdateSite();

		// Get new version
		$this->latestVersion = $model->getLatestVersion();

		// Get installed version from xml file
		$xml           = JFactory::getXML(JPATH_COMPONENT . '/jmailalerts.xml');
		$version       = (string) $xml->version;
		$this->version = $version;

		// Set toolbar
		$this->addToolbar();
		JmailalertsHelper::addSubmenu('dashboard');

		if (JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_TITLE_DASHBOARD'), 'dashboard.png');
		JToolBarHelper::preferences('com_jmailalerts', 550, 875);
	}
}
