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

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Jmailalerts model.
 */
class JmailalertsModelsubscriber extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_JMAILALERTS';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Subscriber', $prefix = 'JmailalertsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_jmailalerts.subscriber', 'subscriber', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jmailalerts.edit.subscriber.data', array());

		if (empty($data)) {
			$data = $this->getItem();

		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__jma_subscribers');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}
    function preview()
    {
		$input=JFactory::getApplication()->input;
		jimport('joomla.filesystem.file');
		require_once(JPATH_SITE . DS .'components'. DS .'com_jmailalerts'. DS .'models'. DS .'emails.php');
		require_once(JPATH_SITE . DS .'components'. DS .'com_jmailalerts'. DS .'helpers'. DS .'emailhelper.php');
		$jmailalertsemailhelper=new jmailalertsemailhelper();
		$jmailalertsModelEmails=new jmailalertsModelEmails();
		$nofrm=0;
		$today=JRequest::getString('select_date_box');
		$email_status=0;
		$target_user_id=JRequest::getInt('user_id');
		$alert_type_id=JRequest::getInt('alert_id');
		$target_email_id=$input->get('email_id','','STRING');
		$destination_email_address=JRequest::getVar('send_mail_to_box');
		$flag=JRequest::getVar('flag'); //print_r($alert_type_id);die("in priview of model");
		if($target_user_id) //for registered user
		{
			$query="SELECT  u.id as user_id,u.name,u.email as email_id,a.template, a.email_subject, e.date, e.alert_id ,a.template_css, e.plugins_subscribed_to, a.respect_last_email_date"
					. " FROM #__users AS u, #__jma_subscribers AS e , #__jma_alerts AS a"
					. " WHERE e.user_id = ".$target_user_id
					. " AND e.alert_id = ".$alert_type_id
					. " AND u.id = e.user_id"
					. " AND a.id = e.alert_id";
		}
		else //guest user
		{
			$query="SELECT  e.user_id as user_id,e.name,e.email_id as email_id,
							a.template, a.email_subject,
							e.date, e.alert_id ,a.template_css, e.plugins_subscribed_to, a.respect_last_email_date"
					. " FROM #__jma_subscribers AS e , #__jma_alerts AS a"
					. " WHERE e.email_id = '".$target_email_id
					. "' AND e.alert_id = ".$alert_type_id
					. " AND a.id = e.alert_id";
		}
		$this->_db->setQuery($query);
		$target_user_data = $this->_db->loadObjectList();

		$i=0;
		foreach($target_user_data as $data)
		{

			if($data->date)
			{
			   //$data[$i]->date = $today;
			   $data->date = ($today) ? $today:$data->date;

		     }
		    else
		    {
				$data[$i]->date = ($today) ? $today:$data[$i]->date;

			}
		$i++;
	    }

		if($target_user_data)
		{
			$target_user_data[0]->email = $destination_email_address;
			//get template from alert type
			$query ="SELECT template FROM #__jma_alerts WHERE id =$alert_type_id ";
			$this->_db->setQuery($query);
			$msg_body= $this->_db->loadResult();  //print_r($msg_body);die("in the model on manage user");
			$skip_tags=array('[SITENAME]','[NAME]','[SITELINK]','[PREFRENCES]', '[mailuser]');
			$tmpl_tags=$jmailalertsModelEmails->get_tmpl_tags($msg_body,$skip_tags);
			$remember_tags=$jmailalertsModelEmails->get_original_tmpl_tags($msg_body,$skip_tags);
			$response=$jmailalertsemailhelper->getMailcontent($target_user_data[0],$flag,$tmpl_tags,$remember_tags);
			if($response[1]==3)
			{
				echo JText::_('COM_JMAILALERTS_NO_MAIL_CONTENT');
				return;
			}
			return $response;

		}
}
}
