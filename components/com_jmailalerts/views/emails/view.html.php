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

jimport('joomla.application.component.view');
jimport('joomla.form.form');

/**
 * HTML Emails View class for the JMA component
 *
 * @since  1.5
 */
class JmailalertsViewEmails extends JViewLegacy
{
	protected $altid;

	protected $cntalert;

	protected $defaultoption;

	protected $default_setting;

	protected $page_title;

	protected $params;

	protected $print;

	protected $qry_concat;

	protected $user;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->params = JComponentHelper::getParams('com_jmailalerts');
		$this->user   = JFactory::getUser();
		$model        = $this->getModel();

		// Get no of count alert
		$cntalert       = $model->gettotalalertcount();
		$this->cntalert = $cntalert;

		if (trim($cntalert) != 0)
		{
			// Creating query for concat from enable plugin for compair to user selected alert
			$qry_concat = $model->alertqryconcat();
			$this->qry_concat = $qry_concat;

			// Get the default alert user selected alerts or default alerts
			$defaultoption       = $model->getdefaultalertid();
			$this->defaultoption = $defaultoption;

			// Checking user default alert id or not
			$default_setting       = $model->isdefaultset();
			$this->default_setting = $default_setting;

			// Getting all alert created alert ids
			$altid       = $model->get_all_alertid();
			$this->altid = $altid;
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_JMAILALERTS_VIEW_TITLE_EMAIL_PREFERENCES'));
		}

		$title = $this->params->def('page_title', JText::_('COM_JMAILALERTS_VIEW_TITLE_EMAIL_PREFERENCES'));

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->page_title = $title;
		$this->document->setTitle($title);

		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
