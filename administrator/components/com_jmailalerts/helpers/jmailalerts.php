<?php
/**
 * @version     2.5
 * @package     com_jmailalerts
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Techjoomla <extensions@techjoomla.com> - http://techjoomla.com
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Jmailalerts helper.
 */
class JmailalertsHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		if(JVERSION>=3.0)
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_DASHBOARD'),
				'index.php?option=com_jmailalerts&view=dashboard',
				$vName == 'dashboard'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_FREQUENCIES'),
				'index.php?option=com_jmailalerts&view=frequencies',
				$vName == 'frequencies'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_ALERTS'),
				'index.php?option=com_jmailalerts&view=alerts',
				$vName == 'alerts'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_SYNC'),
				'index.php?option=com_jmailalerts&view=sync',
				$vName == 'sync'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JMAILALERTS_MAILSIMULATE'),
				'index.php?option=com_jmailalerts&view=mailsimulate',
				$vName == 'mailsimulate'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_SUBSCRIBERS'),
				'index.php?option=com_jmailalerts&view=subscribers',
				$vName == 'subscribers'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JMAILALERTS_HEALTHCHECK'),
				'index.php?option=com_jmailalerts&view=healthcheck',
				$vName == 'healthcheck'
			);
		}
		else
		{
			JSubMenuHelper::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_DASHBOARD'),
				'index.php?option=com_jmailalerts&view=dashboard',
				$vName == 'dashboard'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_FREQUENCIES'),
				'index.php?option=com_jmailalerts&view=frequencies',
				$vName == 'frequencies'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_ALERTS'),
				'index.php?option=com_jmailalerts&view=alerts',
				$vName == 'alerts'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_SYNC'),
				'index.php?option=com_jmailalerts&view=sync',
				$vName == 'sync'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_JMAILALERTS_MAILSIMULATE'),
				'index.php?option=com_jmailalerts&view=mailsimulate',
				$vName == 'mailsimulate'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_JMAILALERTS_TITLE_SUBSCRIBERS'),
				'index.php?option=com_jmailalerts&view=subscribers',
				$vName == 'subscribers'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_JMAILALERTS_HEALTHCHECK'),
				'index.php?option=com_jmailalerts&view=healthcheck',
				$vName == 'healthcheck'
			);
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_jmailalerts';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Method to get the number of users subscribe for alerts
	*/
	public function getSubscribesCount($alertid)
	{
		$db=JFactory::getDBO();

		//get the all alert id
		if($alertid==0) // This getSubscribesCount method call from Manageuser view for multibel alerts &  its alertid=0, If it call from syn view then alert id !=0 
		{
			$query=$db->getQuery(true);
			$query->select('alert.id as alerts_id');
			$query->from('`#__jma_alerts` as alert');
			$query->group('alert.id');
			$db->setQuery($query);
			$alerts_id=$db->loadColumn();
		}
		else
		{
			$alerts_id[0]=$alertid;
		}

		$alert_subscribed_report=array();
		foreach($alerts_id as $single_alert)
		{
			//Get the registered user count against alert
			$query=$db->getQuery(true);
			$query->select('count(subs.user_id) as registered_users');
			$query->from('`#__jma_subscribers` as subs');
			$query->join('LEFT','`#__jma_alerts` as alerts ON alerts.id=subs.alert_id');
			$query->where('subs.user_id>0 AND subs.frequency <> 0 AND subs.alert_id='.$single_alert);
			$db->setQuery($query);
			$alert_subscribed_report[$single_alert]['registed_users']=$registed_users=$db->loadResult();

			//Get the guest user count
			$query=$db->getQuery(true);
			$query->select('count(subs.user_id) as guest_users');
			$query->from('`#__jma_subscribers` as subs');
			$query->join('LEFT','`#__jma_alerts` as alerts ON alerts.id=subs.alert_id');
			$query->where('subs.user_id=0 AND subs.frequency <> 0 AND subs.alert_id='.$single_alert);
			$db->setQuery($query);
			$alert_subscribed_report[$single_alert]['guest_users']=$guest_users=$db->loadResult();

			//get the unsubscribed registerd users
			$query=$db->getQuery(true);
			$query->select('count(subs.user_id) as unsubscribed_users');
			$query->from('`#__jma_subscribers` as subs');
			$query->join('LEFT','`#__jma_alerts` as alerts ON alerts.id=subs.alert_id');
			$query->where('subs.user_id>0 AND subs.frequency=0 AND subs.alert_id='.$single_alert);
			$db->setQuery($query);
			$alert_subscribed_report[$single_alert]['unsubscribed_users']=$unsubscribed_users=$db->loadResult();

			//get the unsubscribed guest users
			$query=$db->getQuery(true);
			$query->select('count(subs.user_id) as unsub_guest_users,alerts.id as alert_id');
			$query->from('`#__jma_subscribers` as subs');
			$query->join('LEFT','`#__jma_alerts` as alerts ON alerts.id=subs.alert_id');
			$query->where('subs.user_id=0 AND subs.frequency=0 AND subs.alert_id='.$single_alert);
			$db->setQuery($query);
			$alert_subscribed_report[$single_alert]['unsub_guest_users']=$unsub_guest_users=$db->loadResult();


			//start not_opted_user for alert ************************** //not_opted_user for alert

			//get the alert subscribed users id
			$query=$db->getQuery(true);
			$query->select('subs.user_id');
			$query->from('`#__jma_subscribers` as subs');
			$query->where('subs.alert_id='.$single_alert);
			$db->setQuery($query);
			$user=$db->loadColumn();
			//not_opted_user for alert
			if(!empty($user))
			{
				//get the users count who not subscribed for this alert
				$user=implode(',',$user);
				//get the user count form #__users where user not in subscriber
				$query=$db->getQuery(true);
				$query->select('count(user.id) as not_opted_user');
				$query->from('`#__users` as user');
				$query->where('user.id NOT IN ('.$user.')');
				$db->setQuery($query);
				$not_opted_user=$db->loadResult();
				$alert_subscribed_report[$single_alert]['not_opted_user']=$not_opted_user;
			}
			else
			{
				//get the $user is empty means no user subscribe for this alert , get the all user from users table
				$query=$db->getQuery(true);
				$query->select('count(user.id) as not_opted_user');
				$query->from('`#__users` as user');
				$db->setQuery($query);
				$not_opted_user=$db->loadResult();
				$alert_subscribed_report[$single_alert]['not_opted_user']=$not_opted_user;
			}
			//end not_opted_user for alert ************************** //not_opted_user for alert
		}
		return $alert_subscribed_report;
	}
}
