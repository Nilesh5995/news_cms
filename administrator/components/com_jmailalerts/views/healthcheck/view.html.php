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
 * Healthcheck view class
 *
 * @since  2.5.0
 */
class JmailalertsViewHealthcheck extends JViewLegacy
{
	protected $data;

	protected $plugins_name;

	protected $sidebar;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		// Get the model
		$model = $this->getModel();

		// Get the plugin names under email-alerts
		$this->data         = $model->healthcheck();
		$this->plugins_name = $model->getPlugnames();

		JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_HEALTHCHECK'), 'wrench');

		JmailalertsHelper::addSubmenu('healthcheck');
		JToolBarHelper::preferences('com_jmailalerts');

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}
}
