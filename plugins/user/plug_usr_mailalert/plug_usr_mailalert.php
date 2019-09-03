<?php
/**
 * @package     JMailAlerts
 * @subpackage  plug_usr_mailalert
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

/**
 * JMA uer plugin
 *
 * @since  1.5.0
 */
class PlgUserPlug_Usr_Mailalert extends JPlugin
{
	/**
	 * Add/update entries for email subscriptions for user being edted
	 *
	 * @param   array    $user    entered user data
	 * @param   boolean  $isNew   true if this is a new user
	 * @param   boolean  $result  true if saving the user worked
	 * @param   string   $error   error message
	 *
	 * @return  boolean
	 */
	public function onUserAfterSave($user, $isNew, $result, $error)
	{
		$db     = JFactory::getDBO();
		$userid = $user['id'];

		if ($isNew)
		{
			$query  = $db->getQuery(true);

			// Get new user details
			$query->SELECT(' id, name, email');
			$query->from('`#__users`');
			$query->where('id = ' . $userid);
			$db->setQuery($query);
			$user_data = $db->loadObject();

			// Get array of alert ids which are set to default for new users.
			$query = 'SELECT id FROM #__jma_alerts WHERE is_default = 1 AND state =  1';
			$db->setQuery($query);
			$alertid 		 = $db->loadColumn();
			$alertid_string = $alertid;

			$alertqry = "";

			if (count($alertid_string))
			{
				for ($i = 0; $i < count($alertid_string); $i++)
				{
					$alertqry .= " id = " . $alertid_string[$i];

					if ($i != (count($alertid_string) - 1) )
					{
						$alertqry .= " OR ";
					}
				}
			}
			else
			{
				return;
			}

			$query = "SELECT element FROM #__extensions WHERE folder = 'emailalerts'  AND enabled = 1";
			$db->setQuery($query);
			$plugnamecompair	 = $db->loadColumn();
			$plugnamesend 		 = implode(',', $plugnamecompair);
			$plugnamecompair	 = explode(',', $plugnamesend);

			$cnt = 0;
			$rnt = 99;

			if (!empty($alertqry))
			{
				$query = "SELECT id, default_freq, template FROM #__jma_alerts WHERE " . $alertqry;
				$db->setQuery($query);
				$result = $db->loadObjectList();
			}
			else
			{
				return;
			}

			if (count($result))
			{
				foreach ($result as $key)
				{
					$email_alert_entry_object 			 = new stdClass;
					$email_alert_entry_object->user_id   = $userid;
					$email_alert_entry_object->alert_id  = $key->id;
					$email_alert_entry_object->frequency = $key->default_freq;
					$email_alert_entry_object->date		 = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $key->default_freq, date("Y")));

					$entry = "";

					if ($user_data)
					{
						$email_alert_entry_object->name     = $user_data->name;
						$email_alert_entry_object->email_id = $user_data->email;
					}

					if ( count($plugnamecompair) )
					{
						for ($i = 0; $i < count($plugnamecompair); $i++)
						{
							if (strstr($key->template, $plugnamecompair[$i]))
							{
								$plugin_name_string[] = $plugnamecompair[$i];
							}
						}
					}

					if (count($plugin_name_string) )
					{
						foreach ($plugin_name_string as $plug)
						{
							$query = "SELECT params
							 FROM #__extensions
							 WHERE element = '" . $plug . "'
							 AND folder = 'emailalerts' ";
							$db->setQuery($query);
							$plug_params = $db->loadResult();

							if (preg_match_all('/\[(.*?)\]/', $plug_params, $match))
							{
								foreach ($match[1] as $mat)
								{
									$match = str_replace(',', '|', $mat);
									$plug_params = str_replace($mat, $match, $plug_params);
								}
							}

							$newlin = explode(",", $plug_params);

							foreach ($newlin as $v)
							{
								if (!empty($v))
								{
									$v = str_replace('{', '', $v);
									$v = str_replace(':', ' = ', $v);
									$v = str_replace('"', '', $v);
									$v = str_replace('}', '', $v);
									$v = str_replace('[', '', $v);
									$v = str_replace(']', '', $v);
									$v = str_replace('|', ',', $v);

									/*if ($plug = = 'jma_latestnews_js')
									{
										$cnt++;
									}*/

									if (!($cnt > $rnt))
									{
										$entry .= $plug . '|' . $v . "\n";
									}
								}

								/*if ($plug = = 'jma_latestnews_js')
								{
									$entry = str_replace('category', 'catid', $entry);
									$entry = str_replace('sections', 'secid', $entry);
								}*/
							}

							$cnt = 0;
						}
					}

					unset($plugin_name_string);
					unset($match);

					$email_alert_entry_object->plugins_subscribed_to = $entry;

					if (!$db->insertObject('#__jma_subscribers', $email_alert_entry_object))
					{
						echo "Insertion error";
						exit;
					}
				}
			}
		}
		else
		{
			$query = $db->getQuery(true);

			// Fields to update.
			$fields = array(
				$db->quoteName('email_id') . ' = ' . $db->quote($user['email'])
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('user_id') . ' = ' . $userid
			);

			$query->update($db->quoteName('#__jma_subscribers'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Remove all email alert subscriptions for the user
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was successfully stored in the database
	 * @param   string   $msg      Message
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__jma_subscribers'))
			->where($db->quoteName('user_id') . ' = ' . (int) $user['id']);

		$db->setQuery($query)->execute();

		return true;
	}
}
