<?php
/**
 * @package     JMailAlerts
 * @subpackage  com_jmailalerts
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * JMailAlerts Component Controller
 *
 * @since  1.0
 */
class JmailalertsController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$this->input = JFactory::getApplication()->input;
		$vName = $this->input->get('view', 'emails');
		$this->input->set('view', $vName);

		$vLayout = 'default';
		$this->input->set('layout', $vLayout);

		parent::display($cachable, $urlparams);
	}

	/**
	 * Method to save preferences
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function savePref()
	{
		$model = $this->getModel('emails');
		$model->savePref();
	}

	/**
	 * Method to send mails
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function processMailAlerts()
	{
		$model = $this->getModel('emails');
		$model->processMailAlerts();
	}
}
