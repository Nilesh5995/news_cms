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

class HelloWorldViewAjax extends JViewLegacy
{

	protected $ajax= null;
	public $data_array;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the layout file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{

		// Get the form to display
		
		$this->ajax = $this->get('Form');
		$this->data_array;
		parent::display($tpl);
		$this->addAjax();
		if($this->ajax)
		{
			return $this->ajax;
		}
		else
		{
			return $this->data_array;
		}
		
	}
	function addAjax() 
	{
		$document = JFactory::getDocument();

		// everything's dependent upon JQuery
		JHtml::_('jquery.framework');

		// we need the Openlayers JS and CSS libraries
		$document->addScript("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.js");
		$document->addStyleSheet("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.css");
		//$document->addStyleSheet("https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");
		// ... and our own JS and CSS
		$document->addScript(JURI::root() . "media/com_helloworld/js/ajax.js");
		// get the data to pass to our JS code
		$params = $this->get("mapParams");
		$document->addScriptOptions('params', $params);
	}

}