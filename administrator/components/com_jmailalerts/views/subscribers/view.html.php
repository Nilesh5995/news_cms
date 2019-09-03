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
class JmailalertsViewSubscribers extends JViewLegacy
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
		$this->filterOptionsAlert = $this->get('FilterOptionsAlert');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		JmailalertsHelper::addSubmenu('subscribers');

		$this->addToolbar();

		if (JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

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
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_TITLE_SUBSCRIBERS'), 'list');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_TITLE_SUBSCRIBERS'), 'subscribers.png');
		}

		//Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/subscriber';
		if (file_exists($formPath)) {
			if ($canDo->get('core.create')) {
				JToolBarHelper::addNew('subscriber.add','JTOOLBAR_NEW');
			}

			if ($canDo->get('core.edit') && isset($this->items[0])) {
				JToolBarHelper::editList('subscriber.edit','JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state')) {
			if (isset($this->items[0]->state)) {
				JToolBarHelper::divider();
				JToolBarHelper::custom('subscribers.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('subscribers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			} else if (isset($this->items[0])) {
				//If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'subscribers.delete','JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state)) {
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('subscribers.archive','JTOOLBAR_ARCHIVE');
			}
			if (isset($this->items[0]->checked_out)) {
				JToolBarHelper::custom('subscribers.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		//Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state)) {
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
				JToolBarHelper::deleteList('', 'subscribers.delete','JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			} else if ($canDo->get('core.edit.state')) {
				JToolBarHelper::trash('subscribers.trash','JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_jmailalerts');
		}

		//Set sidebar action - New in 3.0
		if(JVERSION >= '3.0')
		{
			JHtmlSidebar::setAction('index.php?option=com_jmailalerts&view=subscribers');
			$this->extra_sidebar = '';
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
			);

			//Filter for the field alert_id
			$select_label = JText::sprintf('COM_JMAILALERTS_FILTER_SELECT_LABEL', 'Alert Details');
			$options = array();

			/*$options[0] = new stdClass();
			$options[0]->value = "1";
			$options[0]->text = "Daily";
			$options[1] = new stdClass();
			$options[1]->value = "7";
			$options[1]->text = "Weekly";*/

			$options = $this->filterOptionsAlert;

			JHtmlSidebar::addFilter(
				$select_label,
				'filter_alert_id',
				JHtml::_('select.options', $options , "id", "title", $this->state->get('filter.alert_id'), true)
			);
		}
	}

	protected function getSortFields()
	{
		return array(
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.user_id' => JText::_('COM_JMAILALERTS_SUBSCRIBERS_USER_ID'),
			'a.alert_id' => JText::_('COM_JMAILALERTS_SUBSCRIBERS_ALERT_ID'),
			'a.name' => JText::_('COM_JMAILALERTS_SUBSCRIBERS_NAME'),
			'a.frequency' => JText::_('COM_JMAILALERTS_SUBSCRIBERS_FREQUENCY'),
			'a.date' => JText::_('COM_JMAILALERTS_SUBSCRIBERS_DATE')
		);
	}
}
