<?php
/**
 * @package     JMailAlerts
 * @subpackage  com_jmailalerts
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for a list of Jmailalerts.
 *
 * @since  2.5.0
 */
class JmailalertsViewAlerts extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');

		foreach ($this->items as $item)
		{
			$model           = $this->getModel('alerts');
			$item->plg_names = $model->getPlugnames($item->template);
		}

		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Get the number of users subscribe for alerts
		$JmailalertsHelper = new JmailalertsHelper;
		$this->subsreport  = $JmailalertsHelper->getSubscribesCount(0);

		JmailalertsHelper::addSubmenu('alerts');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/jmailalerts.php';

		$state = $this->get('State');
		$canDo = JmailalertsHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_TITLE_ALERTS'), 'list');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/alert';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('alert.add', 'JTOOLBAR_NEW');
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('alert.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('alerts.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('alerts.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				JToolbarHelper::makeDefault('alerts.setDefault', 'COM_JMAILALERTS_TOOLBAR_SET_SETDEFAULT');
				JToolBarHelper::custom('alerts.unsetDefault', 'unfeatured state notdefault', '', 'COM_JMAILALERTS_TOOLBAR_UNSET_DEFAULT', false);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'alerts.delete', 'JTOOLBAR_DELETE');
			}

			JToolbarHelper::divider();

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('alerts.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('alerts.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'alerts.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('alerts.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_jmailalerts');
		}

		// Set sidebar action - New in 3.0
		if (JVERSION >= '3.0')
		{
			JHtmlSidebar::setAction('index.php?option=com_jmailalerts&view=alerts');
		}

		$this->extra_sidebar = '';

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
		);

		// Filter for the field is_default
		$select_label      = JText::sprintf('COM_JMAILALERTS_FILTER_SELECT_LABEL', 'Is Default');
		$options           = array();
		$options[0]        = new stdClass;
		$options[0]->value = "1";
		$options[0]->text  = JText::_('JYES');
		$options[1]        = new stdClass;
		$options[1]->value = "0";
		$options[1]->text  = JText::_('JNO');

		JHtmlSidebar::addFilter(
			$select_label,
			'filter_is_default',
			JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.is_default'), true)
		);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   2.5.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('COM_JMAILALERTS_ALERTS_TITLE'),
			'a.description' => JText::_('COM_JMAILALERTS_ALERTS_DESCRIPTION'),
			'a.is_default' => JText::_('COM_JMAILALERTS_ALERTS_IS_DEFAULT'),
			'a.default_freq' => JText::_('COM_JMAILALERTS_ALERTS_DEFAULT_FREQ'),
		);
	}
}
