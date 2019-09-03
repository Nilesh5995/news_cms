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

jimport( 'joomla.application.component.view');
jimport('joomla.application.component.model');

class jmailalertsViewajaxsync extends JViewLegacy
{
	function display($tpl = null)
	{
		//Get the instance to joomla database
		$db	= JFactory::getDBO();
		$input=JFactory::getApplication()->input;

		//get the all variables value form ajax request from ajax request url
		$set_firs_ajax_call=$input->get('set_firs_ajax_call','','INT'); // if this variable value is zero the it identifies that this is the first ajax request to get the total number of users
		$alertid=$input->get('alertid','0','INT');
		$last_email_date=$input->get('last_email_date','','STRING');
		$default_frequency=$input->get('default_frequency','0','INT');
		$batch_size=$input->get('batch_size','400','INT');
		$advanced_options_checked=$input->get('advanced_options_checked','0','INT');
		$overwrite_user_pref=$input->get('overwrite_user_pref','0','INT');
		$readd_unsub_user=$input->get('readd_unsub_user','0','INT');

		if($set_firs_ajax_call==0) //if this variable value is zero the it identifies that this is the first ajax request to get the total number of users
		{
			//Overwrite existing user prefereces yes & readd usub user is yes (both are yes then)
			//delete the all entries from subscriber table for current alert id
			if($overwrite_user_pref AND $readd_unsub_user)
			{
				$db	= JFactory::getDBO();
				$db->setQuery("DELETE FROM #__jma_subscribers
				WHERE alert_id=".$alertid." AND user_id <> 0");
				$db->execute();
			}
			else if($overwrite_user_pref) //Overwrite existing user prefereces is 'yes' & readd usub user is 'No' then delete the entries from subscriber table for current 'alert id' Where 'frequency' not zero (means don't delete the unsubscribe users entries)
			{
				$db	= JFactory::getDBO();
				$db->setQuery("DELETE FROM #__jma_subscribers
				WHERE alert_id=".$alertid."
				 AND frequency <> 0 AND user_id <> 0");
				$db->execute();
			}
			//get the user to sync
			//Get limited (decided by the batch_size) users from the joomla users table
		}
		$db->setQuery("SELECT id,name,email FROM #__users WHERE id NOT IN (SELECT user_id FROM  #__jma_subscribers WHERE alert_id=$alertid) AND block=0 ORDER BY id LIMIT  0, ".$input->get('batch_size','400','INT'));
		$usersTosync=$db->loadObjectList();

		$db->setQuery("SELECT count(id) as usercnt FROM #__users WHERE id NOT IN (SELECT user_id FROM  #__jma_subscribers WHERE alert_id=$alertid) AND block=0 ORDER BY id");
		$total_number_of_users=$db->LoadObject();

		//if there are no rows, then all users ar synced; return 'No rows'
		if(count($usersTosync)==0){
			echo "No Users";
		}
		else
		{
			echo $total_number_of_users->usercnt;
		}

		/**
		 * identify the alertid entry in old_sync_data
		*/
		$db->setQuery("SELECT id FROM #__jma_old_sync_data
		WHERE alert_id=$alertid
		");
		$alert_present=$db->loadResult();

		//exit;
 		$alertqry=NULL;
		// load the template & default frequencies for the selected alert
		$query="SELECT id,default_freq,template FROM #__jma_alerts WHERE id=$alertid";
		$db->setQuery($query);
		$alertdata=$db->loadObject();

		//FIRST GET THE EMAIL-ALERTS RELATED PLUGINS
		$db->setQuery('SELECT element FROM #__extensions WHERE folder = \'emailalerts\'  AND enabled = 1');
		$plg_installed = $db->loadColumn();//Get the plugin names and store in an array
		//return the array eg. Array ( [0] => jma_latestdownload [1] => jma_latestusers [2] => jma_latestnews_js )
		//$plg_in_template store the plg-ins actualy used in template
		$plg_in_template=array();
		for($i=0;$i<count($plg_installed);$i++)
		{
			if (strstr($alertdata->template,$plg_installed[$i]))
				$plg_in_template[] =$plg_installed[$i];
		}
		$entry="";
		foreach($plg_in_template as $plug)
		{
			$query= "select params from #__extensions where element='".$plug."' && folder='emailalerts'";
			$db->setQuery($query);
			$plug_params=$db->loadResult();
			$param_list=json_decode($plug_params,true);

			/**
			 * Add the entries in old_sync_data if alert id not present in old_sunc_data
			*/
			if(!$alert_present)
			{
				$old_sync_data=new stdClass;
				$old_sync_data->date=date("Y-m-d H:i:s",mktime(0, 0, 0, date("m"), date("d"), date("Y")));//Get the current date and time
				$old_sync_data->alert_id=$alertid;
				$old_sync_data->plugin=$plug;
				$old_sync_data->plg_data=$plug_params;
				if(!$db->insertObject('#__jma_old_sync_data', $old_sync_data)){
					echo "Insertion error";
					//echo $db->stderr();
					exit;
				}
			}

			// json_decode gives array from plug_params
			$plugentry="";
			foreach($param_list as $key => $value)
			{
					if(is_array($value))  // if value is array such as catagory,catid etc is an array
					{
						// converT array of catid,catagory etc to list and then make string with seperated by comma
						 $selected=implode(',', $value);
						 $plugentry.=$plug.'|'.$key."=".$selected."\n";
					}
					else
					{
						$plugentry.=$plug.'|'.$key."=".$value."\n";
					}

			}
			if($plug == 'jma_latestnews_js')
				$plugentry=  str_replace('category','catid',$plugentry);
			$entry.=$plugentry;  //plugentry for specific  plugin
		}

		//Start of the mega-loop to insert data into the `email_alert` table
		$email_alert_entry_object = new stdClass;
		$email_alert_entry_object->alert_id=$alertid;
		$email_alert_entry_object->frequency=$alertdata->default_freq;
		$email_alert_entry_object->date =$last_email_date;
		foreach($usersTosync as $user)
		{
			$email_alert_entry_object->user_id=$user->id;
			$email_alert_entry_object->name=$user->name;
			$email_alert_entry_object->email_id=$user->email;
			$email_alert_entry_object->plugins_subscribed_to = $entry;

			if(!$db->insertObject('#__jma_subscribers', $email_alert_entry_object)){
				echo "Insertion error";
				//echo $db->stderr();
				exit;
			}
		}
	}//display() ends
}//class JphplistViewajaxsync ends
