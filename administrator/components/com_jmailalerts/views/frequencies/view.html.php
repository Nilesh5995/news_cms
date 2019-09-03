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
 * View class for a list of Jmailalerts.
 */
class JmailalertsViewFrequencies extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		JmailalertsHelper::addSubmenu('frequencies');

		$this->addToolbar();

		if(JVERSION>=3.0)
			$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/jmailalerts.php';

		$state	= $this->get('State');
		$canDo	= JmailalertsHelper::getActions($state->get('filter.category_id'));

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_TITLE_FREQUENCIES'), 'list');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_TITLE_FREQUENCIES'), 'frequencies.png');
		}

		//Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/frequency';
		if (file_exists($formPath)) {
			if ($canDo->get('core.create')) {
				JToolBarHelper::addNew('frequency.add','JTOOLBAR_NEW');
			}

			if ($canDo->get('core.edit') && isset($this->items[0])) {
				JToolBarHelper::editList('frequency.edit','JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state')) {
			if (isset($this->items[0]->state)) {
				JToolBarHelper::divider();
				JToolBarHelper::custom('frequencies.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('frequencies.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			} else if (isset($this->items[0])) {
				//If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'frequencies.delete','JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state)) {
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('frequencies.archive','JTOOLBAR_ARCHIVE');
			}
			if (isset($this->items[0]->checked_out)) {
				JToolBarHelper::custom('frequencies.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		//Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state)) {
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
				JToolBarHelper::deleteList('', 'frequencies.delete','JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			} else if ($canDo->get('core.edit.state')) {
				JToolBarHelper::trash('frequencies.trash','JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_jmailalerts');
		}

		//Set sidebar action - New in 3.0
		if(JVERSION>=3.0)
			JHtmlSidebar::setAction('index.php?option=com_jmailalerts&view=frequencies');

		$this->extra_sidebar = '';

		if(JVERSION>=3.0)
		{
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
			);
		}

		//Filter for the field time_measure
		$select_label = JText::sprintf('COM_JMAILALERTS_FILTER_SELECT_LABEL', 'Time Measure');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "days";
		$options[0]->text  = JText::_('COM_JMAILALERTS_TIME_MEASURE_DAYS');
		$options[1] = new stdClass();
		$options[1]->value = "hours";
		$options[1]->text  = JText::_('COM_JMAILALERTS_TIME_MEASURE_HOURS');
		$options[2] = new stdClass();
		$options[2]->value = "minutes";
		$options[2]->text  = JText::_('COM_JMAILALERTS_TIME_MEASURE_MINUTES');

		if(JVERSION>=3.0)
		{
			JHtmlSidebar::addFilter(
				$select_label,
				'filter_time_measure',
				JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.time_measure'), true)
			);
		}
	}

	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.state' => JText::_('JSTATUS'),
		'a.name' => JText::_('COM_JMAILALERTS_FREQUENCIES_NAME'),
		'a.duration' => JText::_('COM_JMAILALERTS_FREQUENCIES_DURATION'),
		'a.time_measure' => JText::_('COM_JMAILALERTS_FREQUENCIES_TIME_MEASURE'),
		);
	}
}
