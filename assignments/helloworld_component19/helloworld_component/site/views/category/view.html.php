<?php
/*
 * View file for the view which displays a list of helloworld messages in a given category
 */
 
defined('_JEXEC') or die;

class HelloworldViewCategory extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');

		parent::display($tpl);
	}
}