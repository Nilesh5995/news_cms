<?php

/**
 * @package		com_jmailalerts
 * @version		$versionID$
 * @author		TechJoomla
 * @author mail	extensions@techjoomla.com
 * @website		http://techjoomla.com
 * @copyright	Copyright © 2009-2013 TechJoomla. All rights reserved.
 * @license		GNU General Public License version 2, or later
*/

// No direct access
defined('_JEXEC') or die;
class JmailalertsController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/jmailalerts.php';
		$view= JFactory::getApplication()->input->getCmd('view', 'dashboard');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
	public function loadtemplate()
	{
		$model=$this->getModel('alert');
		$model->load_template();

	}
	function loadFrequencies()
	{
		$input=JFactory::getApplication()->input;
		$alertid=$input->get('alertid');

		$model=$this->getModel('sync');

		//$frequencies=$model->getFrequencies($alertid);
		//echo json_encode($frequencies);

		$alert_default_freq=$model->getDefaultFreq($alertid);
		echo json_encode($alert_default_freq);
		jexit();
	}
	function getSubscribesCount()
	{
		$input=JFactory::getApplication()->input;
		$alertid=$input->get('alertid');

		//get the number of users subscribe for alerts
		$jMailAlertshelperPath=JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jmailalerts'.DS.'helpers'.DS.'jmailalerts.php';
		if(!class_exists('JmailalertsHelper'))
		{
		//require_once $path;
		JLoader::register('JmailalertsHelper', $jMailAlertshelperPath );
		JLoader::load('JmailalertsHelper');
		}
		$JmailalertsHelper=new JmailalertsHelper();
		if($alertid>=0)
			$subsreport=$JmailalertsHelper->getSubscribesCount($alertid);
		echo json_encode($subsreport);
		jexit();
	}
	/**
	 * Calls the model method to return email address
	 */

	function simulate(){
		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' ); //debug
		$input=JFactory::getApplication()->input;
		$model =$this->getModel('mailsimulate');

		//$target_user_id=JRequest::getInt('user_id_box');
		$target_user_id=$input->get('user_id_box','','INT');
		if($target_user_id == '') {
			$msg = JText::_( 'COM_JMAILALERTS_ENTR_ID' );
			$this->setRedirect( 'index.php?option=com_jmailalerts&view=mailsimulate', $msg );
		}
		else{

		  $val = $model->simulate();

			if ($val == 1) {
				$msg = JText::_( 'COM_JMAILALERTS_MAIL_SENT' );
				$this->setRedirect( 'index.php?option=com_jmailalerts&view=mailsimulate', $msg );
			}
			elseif($val == 2){
				$msg = JText::_( 'COM_JMAILALERTS_NO_USER' );
				$this->setRedirect( 'index.php?option=com_jmailalerts&view=mailsimulate', $msg );
			}
			elseif($val == 3){
				$msg = JText::_( 'COM_JMAILALERTS_NO_MAIL_SENT' );
				$this->setRedirect( 'index.php?option=com_jmailalerts&view=mailsimulate', $msg );
			}
			 else {
				$msg = JText::_( 'COM_JMAILALERTS_ERROR_SENDING_EMAIL');
				$this->setRedirect( 'index.php?option=com_jmailalerts&view=mailsimulate', $msg, 'error' );
			}
		}
	}//simulate() ends
	/**
	* Calls the model method to return email address
	*/

	function preview()
	{
	$model =$this->getModel( 'subscriber' );
	$model->preview();
	}

	/*
	 * This returns the latest version number from version checker
	 * */
	function getVersion()
	{
		echo $recdata = @file_get_contents('http://techjoomla.com/vc/index.php?key=abcd1234&product=jmailalerts');
		jexit();
	}
}
