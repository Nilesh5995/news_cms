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
 * This is the site view presenting the user with the ability to add a new Helloworld record
 * 
 */
class SchoolViewMarkForm extends JViewLegacy
{
	protected $form = null;
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the layout file to parse.
	 *
	 * @return  void
	 */
	 public function display( $tpl = null)
	 {
	 	$this->form = $this->get('Form');
	 	//print_r($this->form);
	 	//die();
		// Check that the user has permissions to create a new helloworld record
		//$this->canDo = JHelperContent::getActions('com_school');
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Call the parent display to display the layout file
		parent::display($tpl);
		$this->addDocument();
	}
	function addDocument() 
	{
		$document = JFactory::getDocument();

		// everything's dependent upon JQuery
		JHtml::_('jquery.framework');

		// we need the Openlayers JS and CSS libraries
		$document->addScript("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.js");
		$document->addStyleSheet("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.css");
		//$document->addStyleSheet("https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");
		// ... and our own JS and CSS
		$document->addScript(JURI::root() . "components/com_school/models/forms/markform.js");
		// get the data to pass to our JS code
		//$params = $this->get("mapParams");
		//$document->addScriptOptions('params', $params);
	}

	 
}