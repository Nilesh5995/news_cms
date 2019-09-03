<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld View
 *
 * @since  0.0.1
 */
class SchoolViewStudent extends JViewLegacy
{
	/**
	 * View form
	 *
	 * @var         form
	 */
	protected $form = null;
	public $canDo;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display ($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->script = $this->get('Script');

		//$this->canDo = JHelperContent::getActions('com_school', 'Student_List', $this->item->id);
		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = JHelperContent::getActions('com_school', 'student', $this->item->id);
		if (count($errors = $this->get('Errors')))
		{
			//JError::raiseError(500, implode('<br />', $errors));
			throw new Exception(implode("\n", $errors), 500);

			return false;
		}


		// Set the toolbar
		$this->addToolBar();


		// Display the template
		parent::display($tpl);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		// if ($isNew)
		// {
		// 	$title = JText::_('COM_SCHOOL_MANAGER_STUDENT_NEW');
		// }
		// else
		// {
		// 	$title = JText::_('');
		// }

		// JToolbarHelper::title($title, 'student');
		// JToolbarHelper::apply('student.apply');
		// JToolbarHelper::save('student.save');
		// JToolbarHelper::save2new('student.save2new');
		// // // We can save this record, but check the create permission to see if we can return to make a new one.
		// // if ($this->canDo->get('core.create'))
		// // {
		// // 	JToolbarHelper::save2new('student_list.save2new');
		// // }
		// JToolbarHelper::cancel(
		// 	'student.cancel',
		// 	$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		// );
		
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->canDo->get('student.create')) 
			{
				JToolBarHelper::apply('student.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('student.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('student.save2new', 'save-new.png', 'save-new_f2.png',
				                       'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('student.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('student.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('student.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('student.save', 'JTOOLBAR_SAVE');
 
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get('student.create')) 
				{
					JToolBarHelper::custom('student.save2new', 'save-new.png', 'save-new_f2.png',
					                       'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($this->canDo->get('student.create')) 
			{
				JToolBarHelper::custom('student.save2copy', 'save-copy.png', 'save-copy_f2.png',
				                       'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('student.cancel', 'JTOOLBAR_CLOSE');
		}


	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = ($this->item->id < 1);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_SCHOOL_SCHOOL_CREATING') :
                JText::_('COM_SCHOOL_SCHOOL_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_helloworld"
		                                  . "/views/school/submitbutton.js");
		JText::script('COM_SCHOOL_SCHOOL_ERROR_UNACCEPTABLE');
	}
}