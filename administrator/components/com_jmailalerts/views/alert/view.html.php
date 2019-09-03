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
 * View to edit
 */
class JmailalertsViewAlert extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state= $this->get('State');
		$this->item= $this->get('Item');
		$this->form= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);

		if (isset($this->item->checked_out)) {
			$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		} else {
			$checkedOut = false;
		}
		$canDo		= JmailalertsHelper::getActions();

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_TITLE_ALERT'), 'pencil-2');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_JMAILALERTS') . ': ' . JText::_('COM_JMAILALERTS_TITLE_ALERT'), 'alerts.png');
		}

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('alert.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('alert.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('alert.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('alert.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id)) {
			JToolBarHelper::cancel('alert.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('alert.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
