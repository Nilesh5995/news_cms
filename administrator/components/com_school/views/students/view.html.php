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
 * HelloWorlds View
 *
 * @since  0.0.1
 */
class SchoolViewStudents extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		// $this->state         = $this->get('State');
		// $this->filterForm    = $this->get('FilterForm');
		// $this->activeFilters = $this->get('ActiveFilters');
		// What Access Permissions does this user have? What can (s)he do?
		//$this->canDo = JHelperContent::getActions('com_school');
		// Check for errors.
		$this->canDo = JHelperContent::getActions('com_school');	
		if (count($errors = $this->get('Errors')))
		{
			//JError::raiseError(500, implode('<br />', $errors));
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}
		// Display the template
		$this->addToolBar();
		parent::display($tpl);
	}
	// protected function addToolBar()
	// {
	// 	JToolbarHelper::title(JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLDS'));
	// 	JToolbarHelper::addNew('student.add');
	// 	JToolbarHelper::editList('student.edit');
	// 	JToolbarHelper::deleteList('','students.delete');
	// }
	protected function addToolBar()
	{
		$title = JText::_('COM_SCHOOL_MANAGER_HELLOWORLDS');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}

		JToolBarHelper::title($title, 'school');

		if ($this->canDo->get('student.create')) 
		{
			JToolBarHelper::addNew('student.add', 'JTOOLBAR_NEW');
		}
		if ($this->canDo->get('student.edit')) 
		{
			JToolBarHelper::editList('student.edit', 'JTOOLBAR_EDIT');
		}
		if ($this->canDo->get('student.delete')) 
		{
			JToolBarHelper::deleteList('', 'students.delete', 'JTOOLBAR_DELETE');
		}
		if ($this->canDo->get('student.admin')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_school');
		}
	}
	 // Options button.
 //    if (JFactory::getUser()->authorise('core.admin', 'com_school')) 
 //    {
	// JToolBarHelper::preferences('com_school');
 //    }
}