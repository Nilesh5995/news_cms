<?php
/**
 * @package     JMailAlerts
 * @subpackage  com_jmailalerts
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.form.form');

/**
 * Class will contain function to store alerts records
 *
 * @since  1.0.0
 */
class JmailalertsModelEmails extends JModelLegacy
{
	public $log = null;

	/**
	 * Function for retun alert frequency select box checking allowd frequency , default_freq
	 *
	 * @param   int  $altid  Alert id
	 *
	 * @return  array
	 */
	public function getFreq($altid)
	{
		$user  = JFactory::getUser();
		$query = "SELECT title,allowed_freq,default_freq ,allow_users_select_plugins,description FROM #__jma_alerts WHERE id= $altid";
		$this->_db->setQuery($query);
		$resultfrq = $this->_db->loadObjectList();
		$allow_frq = $resultfrq[0]->allowed_freq;

		// $allow_frq = explode(',', $allow_frq);
		$allow_frq = str_replace('[', '', $allow_frq);
		$allow_frq = str_replace(']', '', $allow_frq);
		$allow_frq = str_replace('"', '', $allow_frq);
		$allow_frq = explode(',', $allow_frq);

		// Get frequency name for each allowed frequency.
		foreach ($allow_frq as $key => $value)
		{
			$query = "SELECT name FROM #__jma_frequencies WHERE id=" . $value;
			$this->_db->setQuery($query);
			$frequency_name[$value] = JText::_($this->_db->loadResult());
		}

		$query = "SELECT `frequency` FROM #__jma_subscribers WHERE alert_id = " . $altid . " AND user_id = " . $user->id;
		$this->_db->setQuery($query);
		$uerselfrq = $this->_db->loadResult();

		if ($uerselfrq)
		{
			$default_freq = $uerselfrq;
		}
		else
		{
			$default_freq = $resultfrq[0]->default_freq;
		}

		$setfrqname    = 'c' . $altid;
		$alertfrqdta[] = JHtml::_("select.genericlist", $frequency_name, "$setfrqname", "class='input' ", "value", "text", $default_freq);
		$alertfrqdta[] = $resultfrq[0]->title;
		$alertfrqdta[] = $resultfrq[0]->description;
		$alertfrqdta[] = $resultfrq[0]->allow_users_select_plugins;

		return $alertfrqdta;
	}

	/**
	 * Function to save alter day's
	 * Function called from the controller.php file.
	 * This function is called when the user saves the email preferences(Daily, monthly, weekly, etc) from the frontend
	 *
	 * @return  void
	 */
	public function savePref()
	{
		$app                        = JFactory::getApplication();
		$input                      = JFactory::getApplication()->input;
		$user_freq                  = $input->get('c');
		$unsubscribe_chk_box_status = $input->get('unsubscribe_chk_box');
		$my                         = JFactory::getUser();
		$user_state                 = 1;

		if ($unsubscribe_chk_box_status)
		{
			$user_freq  = 0;
			$user_state = 0;
		}

		$post = JRequest::get('post');
		$alt = $input->get('alt', array(), 'ARRAY');

		// Gt all alert id
		$query = "SELECT id FROM #__jma_alerts";
		$this->_db->setQuery($query);
		$delaltuser = $this->_db->loadColumn();

		$delalt = array();

		for ($i = 0; $i < count($delaltuser); $i++)
		{
			if (!in_array($delaltuser[$i], $alt))
			{
				$delalt[] = $delaltuser[$i];
			}
		}

		// Query construct for delete
		$tmpdel = '';

		for ($i = 0; $i < count($delalt); $i++)
		{
			$tmpdel .= " `alert_id` =" . $delalt[$i];

			if ($i != (count($delalt) - 1))
			{
				$tmpdel .= " OR ";
			}
		}

		if ($tmpdel)
		{
			$tmpdel = "(" . $tmpdel . ") ";
		}

		if ($tmpdel != "" && ($my->id || $post['user_email']))
		{
			// Changed in 2.4.3
			// $delquery = " DELETE FROM #__jma_subscribers WHERE user_id = {$my->id} AND $tmpdel";

			if ($my->id)
			{
				$query_string = " `user_id`=" . $my->id;
			}
			else
			{
				$query_string = " `email_id`='" . $post['user_email'] . "' ";
			}

			$delquery = "UPDATE `#__jma_subscribers` SET `state`=0 WHERE " . $query_string . " AND " . $tmpdel;
			$this->_db->setQuery($delquery);
			$this->_db->execute();
		}

		for ($i = 0; $i < count($alt); $i++)
		{
			$db_plugentry = "";

			if (isset($post['alt']))
			{
				$tmp = 'ch' . $alt[$i];

				if (count($input->get($tmp)) != 0)
				{
					$user_set_plug = array_values($post["$tmp"]);
				}

				$plug_name = array_keys($post);

				// Code for converting the plugin params to store in the database
				foreach ($plug_name as $plug_name)
				{
					if (count($input->get($tmp)) != 0)
					{
						if (in_array($plug_name, $user_set_plug))
						{
							foreach ($post[$plug_name] as $key => $val)
							{
								if (is_array($val))
								{
									$val = implode(',', $val);
								}

								$db_plugentry .= $plug_name . '|' . $key . '=' . $val . "\n";
							}
						}
					}
				}
			}
			else
			{
				// Space is important
				$db_plugentry = " ";
			}

			$db_plugentry = str_replace("_$alt[$i]", "", $db_plugentry);

			$today = date('Y-m-d H:i:s');

			// For registered user
			if ($my->id)
			{
				$query = "SELECT `frequency` FROM #__jma_subscribers WHERE user_id=" . $my->id . " AND alert_id = $alt[$i]";
			}
			else
			{
				$query = "SELECT `frequency` FROM #__jma_subscribers WHERE email_id=" . $this->_db->Quote($post['user_email']) . " AND alert_id = " . $alt[$i];
			}

			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();

			if ($result == null)
			{
				if ($db_plugentry == '')
				{
					$db_plugentry = '';
				}
				else
				{
					// For registered user
					if ($my->id)
					{
						$get_name_email = $this->getname_email($my->id);
						$query          = "INSERT INTO `#__jma_subscribers`(`user_id`,`name`,`email_id`,`alert_id`,`frequency`,`date`,`plugins_subscribed_to`)
							 VALUES (
							 	" . $this->_db->Quote($my->id) . ",
							 	" . $this->_db->Quote($get_name_email['username']) . ",
							 	" . $this->_db->Quote($get_name_email['email']) . ",
							 	" . $this->_db->Quote($alt[$i]) . ",
							 	" . $input->get("c$alt[$i]") . ",
							 	" . $this->_db->Quote($today) . ",
							 	" . $this->_db->Quote($db_plugentry) . "
							)";
					}
					else
					{
						// For guest user...
						$query = "INSERT INTO `#__jma_subscribers` (`user_id`,`alert_id`,`name`,`email_id`,`frequency`,`date`,`plugins_subscribed_to`)
							 VALUES (
							 	0,
							 	" . $this->_db->Quote($alt[$i]) . ",
							 	" . $this->_db->Quote($post['user_name']) . ",
							 	" . $this->_db->Quote($post['user_email']) . ",
							 	" . $input->get("c$alt[$i]") . ",
							 	" . $this->_db->Quote($today) . ",
							 	" . $this->_db->Quote($db_plugentry) . "
							 )";
					}
				}
			}
			else
			{
				$update_query_string = '';

				if ($db_plugentry != '')
				{
					$update_query_string = ", `plugins_subscribed_to` =" . $this->_db->Quote($db_plugentry);

					if ($my->id)
					{
						$query = "UPDATE `#__jma_subscribers`
							 SET `state`= " . $user_state . ",`frequency` = " . $input->get("c$alt[$i]") . "" . $update_query_string . "
							  WHERE `user_id` = {$my->id} " . "AND `alert_id` = $alt[$i]";
					}
					else
					{
						$query = "UPDATE `#__jma_subscribers`
							 SET `state`= " . $user_state . ",`frequency` = " . $input->get("c$alt[$i]") . "" . $update_query_string . "
							  WHERE `email_id` = {$this->_db->Quote($post['user_email'])} " . "AND `alert_id` = $alt[$i]";
					}
				}
				else
				{
					if ($my->id)
					{
						$query = "DELETE FROM #__jma_subscribers WHERE `alert_id` = $alt[$i] AND `user_id` = {$my->id}";
					}
					else
					{
						$query = "DELETE FROM #__jma_subscribers WHERE `alert_id` = $alt[$i] AND `email_id` = " . $this->_db->Quote($post['user_email']);
					}
				}
			}

			// @echo $query;

			$this->_db->setQuery($query);
			$this->_db->execute();
		}

		$msg                    = JText::_('COM_JMAILALERTS_SETTINGS_SAVED_SUCCESSFULLY');
		$jmailalertsModelEmails = new jmailalertsModelEmails;
		$itemid                 = $jmailalertsModelEmails->getItemid();
		$app->redirect(JRoute::_('index.php?option=com_jmailalerts&view=emails&Itemid=' . $itemid, false), $msg);
	}

	/**
	 * Get name and email of the user to store it in db
	 *
	 * @param   int  $user_id  User id
	 *
	 * @return  array
	 */
	public function getname_email($user_id)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT username, email
				FROM #__users
				WHERE id=" . $user_id;
		$db->setQuery($query);
		$name_email_array = $db->loadAssoc();

		// @print_r($name_email_array); die('asdasd');

		return $name_email_array;
	}

	/**
	 * Get Itemid
	 *
	 * @return  int
	 */
	public function getItemid()
	{
		$this->_db->setQuery('SELECT id FROM #__menu WHERE link LIKE "%com_jmailalerts&view=emails%"');
		$item_id = $this->_db->loadResult();

		return $item_id;
	}

	/**
	 * Function to send mails
	 * Since this function sends emails, theres a logging code added to log the info abt email sending
	 * i.e. timestamp, recipient-info, failure/success of email sending.
	 *
	 * @return  void
	 */
	public function processMailAlerts()
	{
		jimport('joomla.filesystem.file');
		$params = JComponentHelper::getParams('com_jmailalerts');

		// @require(JPATH_SITE . DS . "components" . DS . "com_jmailalerts" . DS . "emails" . DS . "config.php");

		$input = JFactory::getApplication()->input;

		// $numberofmails = $params->get('inviter_percent');
		// $enable_batch = $params->get('enb_batch');

		$pkey  = $input->get('pkey', '', 'STRING');
		$today = date('Y-m-d H:i:s');

		$this->log[] = '';
		$this->log[] = JText::sprintf("COM_JMAILALERTS_START", $today);

		if ($pkey != $params->get('private_key_cronjob'))
		{
			$this->log[] = JText::_("COM_JMAILALERTS_NOT_AUTHO");
		}
		else
		{
			// $msg_body = stripslashes($params->get('message_body'));
			$skip_tags = array(
				'[SITENAME]',
				'[NAME]',
				'[SITELINK]',
				'[PREFRENCES]',
				'[mailuser]'
			);

			// Get all tags  from the template with whitespace as it is
			// $remember_tags=$this->get_original_tmpl_tags($msg_body,$skip_tags);

			// Get all tags  from the template removing whitespace in a correct needed array format
			// $tmpl_tags=$this->get_tmpl_tags($msg_body,$skip_tags);
			$batch_size_if_enable = 10;

			// Get all alerts
			$all_published_alerts = $this->get_all_alertid();

			foreach ($all_published_alerts as $key)
			{
				// Calculate all user as per alerts.
				$email_users = array();

				// Get batch size of current alert if batch size is enable..
				// @toDo calculate batch size

				$this->log[]  = JText::sprintf("COM_JMAILALERTS_ALERT_MSG", $key);
				$enable_batch = $this->get_batch_size($key);
				$this->log[]  = JText::sprintf("COM_JMAILALERTS_ENABLE_BATCH", $key, $enable_batch);

				// $enable_batch = 2;

				// Get all the block users
				$query        = "SELECT id FROM #__users WHERE block = 1";
				$this->_db->setQuery($query);
				$block_users       = $this->_db->loadColumn();

				// @print_r($block_users); die('adas');
				$block_users_array = implode(',', $block_users);

				// @print_r($block_users_array); die('asdasd');

				$whr               = '';

				if ($block_users_array)
				{
					$whr = "  AND user_id NOT IN (" . $block_users_array . ") ";
				}

				$query = "SELECT  e.user_id, e.name, e.email_id, e.date, e.plugins_subscribed_to, e.alert_id, e.frequency,
				    a.template, a.template_css, a.email_subject, a.respect_last_email_date
					 FROM #__jma_subscribers AS e ,#__jma_alerts as a
					 WHERE e.alert_id = a.id
					 AND e.frequency > 0
					 AND a.state=1
					 AND e.state=1
					 AND e.alert_id=" . $key . "
					" . $whr;

				$this->_db->setQuery($query);
				$email_eligible_users           = $this->_db->loadObjectList();
				$email_users_without_batch_size = array();

				// @print_r($email_users_without_batch_size); die('asdasd');
				foreach ($email_eligible_users as $key_user)
				{
					// Get frequency time in min
					$to_time            = strtotime($today);
					$from_time          = strtotime($key_user->date);
					$min_diffrence      = round(abs($to_time - $from_time) / 60, 2);

					// @print_r($key_user); die('asdasd');
					$getrequired_minute = $this->getrequired_minute($key_user->frequency);

					// @print_r($getrequired_minute); die('adasdasd');
					if ($min_diffrence >= $getrequired_minute)
					{
						$email_users_without_batch_size[] = $key_user;
					}
				}

				// @print_r($email_users_without_batch_size);
				if ($email_users_without_batch_size)
				{
					if ($enable_batch)
					{
						// @$email_users[] = array_slice($email_users_without_batch_size, 0, $enable_batch);
						$i = 0;

						for ($i = 0; $i < $enable_batch && $i < count($email_users_without_batch_size); $i++)
						{
							$email_users[] = $email_users_without_batch_size[$i];
						}
					}
					else
					{
						foreach ($email_users_without_batch_size as $key)
						{
							$email_users[] = $key;
						}
					}
				}

				if ($email_users)
				{
					$user_count      = count($email_users);
					$this->log[]     = JText::sprintf("COM_JMAILALERTS_FOUND_TO_PRO", $user_count);

					echo implode('<br/>', $this->log);

					// Log details
					$this->storeLog($this->log);

					unset($this->log);
					$this->log[] = '';

					$send_mail    = 0;
					$send_no_mail = 0;

					foreach ($email_users as $email_user)
					{
						$remember_tags = $this->get_original_tmpl_tags($email_user->template, $skip_tags);

						$tmpl_tags              = $this->get_tmpl_tags($email_user->template, $skip_tags);
						$jmailalertsemailhelper = new jmailalertsemailhelper;
						$return_val             = $jmailalertsemailhelper->getMailcontent($email_user, 2, $tmpl_tags, $remember_tags);

						// Explode the array to get the return value as now the return also contain the lof file
						if (isset($return_val[0]))
						{
							$log = $return_val[0];

							foreach ($log as $key)
							{
								$this->log[] = $key;
							}
						}

						$val = '';

						if (isset($return_val[1]))
						{
							$val = $return_val[1];
						}

						if ($val == 1)
						{
							$send_mail++;
						}
						elseif ($val == 3)
						{
							$this->log[] = JText::sprintf("COM_JMAILALERTS_MAIL_SEND_FAIL", $email_user->name, $email_user->user_id);
							$send_no_mail++;
						}
					}

					$this->log[] = JText::sprintf("COM_JMAILALERTS_PRO_OUT_OF", $send_mail, $user_count);

					// @echo JText::sprintf("COM_JMAILALERTS_PRO_OUT_OF", $send_mail, $user_count);
				}
				else
				{
					$this->log[] = JText::_("COM_JMAILALERTS_NO_USER");

					// @echo JText::_("COM_JMAILALERTS_NO_USER");
				}

				$this->log[] = JText::_("COM_JMAILALERTS_FINSH");

				// @echo JText::_("COM_JMAILALERTS_FINSH");
			}
			// Foreach alert.....
		}

		echo implode('<br/>', $this->log);

		$this->storeLog($this->log);
	}

	/**
	 * Store log
	 *
	 * @param   array  $logData  data.
	 *
	 * @since   1.0
	 * @return  list.
	 */
	public function storeLog($logData)
	{
		$jConfig        = JFactory::getConfig();
		$logPath        = $jConfig->get('log_path');
		$logFilePath    = $logPath . '/jmailalerts.php';

		$params         = JComponentHelper::getParams('com_jmailalerts');
		$maxLogFileSize = (int) $params->get('log_file_size', 10);

		// 'MB' => 1024 * 1024,
		$maxLogFileSize = $maxLogFileSize * 1024 * 1024;

		JLoader::import('joomla.filesystem.file');

		// Code for if log file exceeds certain size
		if (JFile::exists($logFilePath))
		{
			// If file size exceeds, rotate file
			if (filesize($logFilePath) > $maxLogFileSize)
			{
				$tempLogFile = $logPath . '/jmailalerts-log-1.php';

				if (JFile::exists($tempLogFile))
				{
					JFile::delete($tempLogFile);
				}

				JFile::copy($logFilePath, $tempLogFile);
				JFile::delete($logFilePath);
			}
		}

		$config = array(
			'text_file' => 'jmailalerts.php'
		);

		// Joomla 3
		jimport('joomla.log.logger.formattedtext');
		$logger = new JLogLoggerFormattedtext($config);

		$finalLogText = implode("\n", $logData);
		$finalLogText .= "\n";

		// FinalLogText is a string
		// $status can be JLog::INFO, JLog::WARNING, JLog::ERROR, JLog::ALL, JLog::EMERGENCY or JLog::CRITICAL
		$entry = new JLogEntry($finalLogText, $status = JLog::INFO);

		$logger->addEntry($entry);
	}

	/**
	 * Get the batch size of all the alert ids.
	 * Function returns the value of the batch size if the batch size is enable.
	 *
	 * @param   int  $alertid  Alert id
	 *
	 * @return  int
	 */
	public function get_batch_size($alertid)
	{
		$query = "SELECT batch_size
				FROM #__jma_alerts
				WHERE enable_batch=1
				AND id=" . $alertid;
		$this->_db->setQuery($query);
		$enable_batch = $this->_db->loadresult();

		// @print_r($enable_batch); die('asdasd');

		return $enable_batch;
	}

	/**
	 * Calculate time diffrence in minute
	 *
	 * @param   int  $frequency_id  [description]
	 *
	 * @return  int
	 */
	public function getrequired_minute($frequency_id)
	{
		// @print_r($frequency_id); die('asdasdasd');

		$query = "SELECT id,time_measure,duration,name
						FROM #__jma_frequencies";
		$this->_db->setQuery($query);
		$frequency = $this->_db->loadObjectList();

		foreach ($frequency as $key_freq => $value_freq)
		{
			if ($value_freq->time_measure == 'days')
			{
				$frquency_in_mim[$value_freq->id] = $value_freq->duration * 24 * 60;
			}
			elseif ($value_freq->time_measure == 'hours')
			{
				$frquency_in_mim[$value_freq->id] = $value_freq->duration * 60;
			}
			elseif ($value_freq->time_measure == 'minutes')
			{
				$frquency_in_mim[$value_freq->id] = $value_freq->duration;
			}
		}

		if (array_key_exists($frequency_id, $frquency_in_mim))
		{
			return $frquency_in_mim[$frequency_id];
		}

		// @return
	}

	/**
	 * Function to call plugins with array of type [param_name]=>param_value and return the output
	 * This function is called from the get_data() function above
	 * [gettriggerPlugins description]
	 *
	 * @param   int     $userid                    User id
	 * @param   string  $last_email_date           Last email date
	 * @param   array   $final_plugin_params_data  Params date
	 * @param   string  $latest                    Latest
	 *
	 * @return  array
	 */
	public function gettriggerPlugins($userid, $last_email_date, $final_plugin_params_data, $latest)
	{
		$results         = array();
		$i               = 0;
		$special_plugins = array();
		$count           = 0;

		// Important
		$flag            = 0;

		$aresults        = array();

		foreach ($final_plugin_params_data as $plug)
		{
			// Check if plugin is to be triggered
			if (isset($plug['plug_trigger']))
			{
				// Check if pluign is special
				if (isset($plug['is_special']))
				{
					// If yes add it in new array to process after proceessing normal plugins
					$special_plugins[$count] = $plug;
					$count++;
				}
				// Normal plugin
				else
				{
					$dispatcher = JDispatcher::getInstance();
					$plug['plug_trigger'];
					JPluginHelper::importPlugin('emailalerts', $plug['plug_trigger']);

					// Triger the plugins
					// Parameters passed are userid,last email date,final plugin trigger data,fetch only latest
					$results = $dispatcher->trigger(
						'onEmail_' . $plug['plug_trigger'],
						array(
							$userid,
							$last_email_date,
							$plug,
							$latest
						)
					);

					if ($results)
					{
						if (!$flag && $results[0][1] != '')
						{
							// Set flag even if a result is outputted by any of the normal plugin
							$flag = 1;
						}

						$results[0][] = $plug['tag_to_replace'];
						$aresults[$i] = $results[0];
						$i++;
					}
				}
			}
		}

		// If content is outputted by normal plugins
		if ($flag)
		{
			foreach ($special_plugins as $plug)
			{
				if (isset($plug['plug_trigger']))
				{
					$dispatcher = JDispatcher::getInstance();
					JPluginHelper::importPlugin('emailalerts', $plug['plug_trigger']);

					// Triger the plugins
					// Parameters passed are userid,last email date,final plugin trigger data,fetch only latest
					$plug['plug_trigger'];
					$results = $dispatcher->trigger(
						'onEmail_' . $plug['plug_trigger'],
						array(
							$userid,
							$last_email_date,
							$plug,
							$latest
						)
					);

					if ($results)
					{
						$results[0][] = $plug['tag_to_replace'];
						$aresults[$i] = $results[0];
						$i++;
					}
				}
			}
		}

		return $aresults;
	}

	/**
	 * Function to get the default alert user selected alerts or default alerts
	 *
	 * @return  array
	 */
	public function getdefaultalertid()
	{
		$user  = JFactory::getUser();
		$query = "SELECT `alert_id`,`frequency`,`state` FROM `#__jma_subscribers` WHERE `user_id` = " . $user->id . " AND `frequency`>0";
		$this->_db->setQuery($query);
		$temp_data = $this->_db->loadObjectList();

		$option = array();

		foreach ($temp_data as $td)
		{
			$opt['frequency']      = $td->frequency;
			$opt['state']          = $td->state;
			$option[$td->alert_id] = $opt;
		}

		if (!$option)
		{
			// Get the frequency from default configuration
			// $query="SELECT alert_id  FROM #__jma_subscribers_Default";

			$query = "SELECT id  FROM #__jma_alerts WHERE is_default = 1 AND state=1 ";
			$this->_db->setQuery($query);
			$temp_data = $this->_db->loadColumn();

			// $temp_data = explode(',',$temp_data);

			$option    = array();

			foreach ($temp_data as $td)
			{
				$opt['frequency'] = 0;
				$option[$td]      = $opt;
			}
		}

		return $option;

		// O/p

		// Array ( [2] => Array ( [option] => 0 ) [3] => Array ( [option] => 0 ) ) in site model jma

		// Array ( [2] => Array ( [option] => 0 ) [3] => Array ( [option] => 0 )
		// [4] => Array ( [option] => 0 ) [5] => Array ( [option] => 0 ) ) in site model jma
	}

	/**
	 * Function for checking user default alert id or not
	 *
	 * @return  int|string
	 */
	public function isdefaultset()
	{
		$user  = JFactory::getUser();
		$query = "SELECT alert_id FROM #__jma_subscribers WHERE user_id = " . $user->id;
		$this->_db->setQuery($query);
		$option = $this->_db->loadColumn();

		if (!$option)
		{
			$default_setting = 1;
		}
		else
		{
			$default_setting = "";
		}

		return $default_setting;
	}

	/**
	 * Function for retun all alert id
	 *
	 * @return  int
	 */
	public function get_all_alertid()
	{
		$query = "SELECT id FROM #__jma_alerts WHERE state=1";
		$this->_db->setQuery($query);
		$altid = $this->_db->loadColumn();

		return $altid;
	}

	/**
	 * Function for retun no of alerts
	 *
	 * @return  int
	 */
	public function gettotalalertcount()
	{
		$query = "SELECT count(*) FROM `#__jma_alerts` WHERE state=1";
		$this->_db->setQuery($query);
		$altid = $this->_db->loadResult();

		return $altid;
	}

	/**
	 * Function for return query concat
	 *
	 * @return  array
	 */
	public function alertqryconcat()
	{
		// Get plugins
		$this->_db->setQuery('SELECT name, element,params FROM #__extensions WHERE folder=\'emailalerts\' AND enabled = 1');
		$test = $this->_db->loadObjectList();

		// Get alerts
		$query = "SELECT template FROM #__jma_alerts WHERE state=1";
		$this->_db->setQuery($query);
		$test2 = $this->_db->loadObjectList();

		// Get the plugin names and store in an array
		$this->_db->setQuery('SELECT element FROM #__extensions WHERE folder = \'emailalerts\'  AND enabled = 1');
		$plugnamecompair = $this->_db->loadColumn();

		$qry_concat = array();

		if ($test2)
		{
			foreach ($test2 as $key)
			{
				$plugin_name_string = array();

				for ($i = 0; $i < count($plugnamecompair); $i++)
				{
					if (strstr($key->template, $plugnamecompair[$i]))
					{
						$plugin_name_string[] = $plugnamecompair[$i];
					}
				}

				$tmp = "";

				for ($i = 0; $i < count($plugin_name_string); $i++)
				{
					$tmp .= " element LIKE '" . $plugin_name_string[$i] . "' ";

					if ($i != (count($plugin_name_string) - 1))
					{
						$tmp .= " OR ";
					}
				}

				$qry_concat[] = $tmp;
				unset($plugin_name_string);
			}
		}

		return $qry_concat;
	}

	/**
	 * Get data
	 *
	 * @param   int  $aid  Alert id
	 *
	 * @return  array
	 */
	public function getData($aid)
	{
		$option = array();
		$user   = JFactory::getUser();

		// Iif ($user->id) {
		// Get the option saved related to the user-id from the email_alert table
		$where  = '';

		if ($user->id)
		{
			$where = ' AND user_id =' . $user->id;
		}

		if ($aid != "")
		{
			$query = "SELECT `frequency`,`plugins_subscribed_to` FROM #__jma_subscribers WHERE alert_id =" . $aid . " " . $where;
			$this->_db->setQuery($query);
			$option = $this->_db->loadRow();
		}

		if ($option[1] != '')
		{
			// @TODO check function call
			$opt = $this->get_frontend_plugin_data($option[1]);

			if ($opt)
			{
				foreach ($opt as $kk => $vv)
				{
					foreach ($vv as $k => $v)
					{
						$opt1[$kk][] = $k . '=' . $v;
					}
				}
			}

			if (isset($opt1))
			{
				$option[1] = $opt1;
			}
		}

		if (!$option)
		{
			// Just installed and not yet synced
			// @TODO needs test , chk function call
			$opt = $this->get_frontend_plugin_data("");

			// $opt=$this->getUserPlugData($option[1]);

			foreach ($opt as $kk => $vv)
			{
				foreach ($vv as $k => $v)
				{
					if ($kk == 'jma_latestnews_js' && $k == 'category')
					{
						$k = 'catid';
					}

					$opt1[$kk][] = $k . '=' . $v;
				}
			}

			if (isset($opt1))
			{
				$option[1] = $opt1;
			}
		}

		return $option[1];

		// }

		// If ends
	}

	/**
	 * Function will return the user settings for the plugins in format of plugin_name {[para_name1]=para_value1,[para_name2]=para_value2,..}
	 * Function is called from the function getData() and from getMailcontent()
	 *
	 * @param   string  $data  Data
	 *
	 * @return  array
	 */
	public function getUserPlugData($data)
	{
		$newline_plugins_array = array();
		$newline_plugins_array = explode("\n", $data);

		foreach ($newline_plugins_array as $line)
		{
			if (!trim($line))
			{
				continue;
			}

			$pcs                               = explode('|', $line);
			$userconfig                        = explode('=', $pcs[1]);
			$userdata[$pcs[0]][$userconfig[0]] = $userconfig[1];
		}

		$i = 0;

		foreach ($userdata as $key => $u)
		{
			$u['plug_trigger'] = $key;
			$u_plugs[$i]       = $u;
			$i++;
		}

		return $u_plugs;
	}

	/**
	 * Function to get the inline css html code from the emogrifier
	 *
	 * @param   string  $html  Email HTML
	 * @param   string  $css   Email CSS
	 *
	 * @return  string
	 */
	public function getEmogrify($html, $css)
	{
		require_once JPATH_SITE . DS . "components" . DS . "com_jmailalerts" . DS . "models" . DS . "emogrifier.php";

		// Condition to check if mbstring is enabled
		if (!function_exists('mb_convert_encoding'))
		{
			echo JText::_("COM_JMAILALERTS_MB_EXT");

			return $html;
		}

		$emogr    = new TJEmogrifier($html, $css);
		$html_css = $emogr->emogrify();

		return $html_css;
	}

	/**
	 * Function to get the plugin data(names, elements) related to emailalert
	 *
	 * @param   string  $qry_concat  Query part to be concated
	 *
	 * @return  object|boolean
	 */
	public function getPluginData($qry_concat)
	{
		// This is important to load lang. constants for plugins in frontend.
		JPluginHelper::importPlugin('emailalerts');
		$dispatcher = JDispatcher::getInstance();

		if ($qry_concat !== '')
		{
			$query = "SELECT name, element, params
			 FROM #__extensions
			 WHERE folder='emailalerts'
			 AND (" . $qry_concat . ")
			 AND enabled=1
			 ORDER BY ordering ASC";

			$this->_db->setQuery($query);
			$plugin_data = $this->_db->loadObjectList();

			if ($plugin_data)
			{
				return $plugin_data;
			}
		}
		else
		{
			return false;
		}
	}

	/*
	 *
	 * ///////////////////////////////////////////////////////
	 * All fuctions below are added in 2.4 version
	 * ///////////////////////////////////////////////////////
	 *
	 */

	/**
	 * Get default plugin params j15
	 *
	 * @param   string  $plugin_name  Plugin name
	 *
	 * @return  array
	 */
	public function get_default_plugin_params_j15($plugin_name)
	{
		$plugin = JPluginHelper::getPlugin('emailalerts', $plugin_name);

		if (!$plugin)
		{
			return false;
		}

		$pluginParams          = new JParameter($plugin->params);
		$pluginParamsDefault   = $pluginParams->_raw;
		$newlin                = explode("\n", $pluginParamsDefault);
		$default_plugin_params = array();

		foreach ($newlin as $v)
		{
			if (!empty($v))
			{
				$v = str_replace('|', ',', $v);
				$v = explode("=", $v);

				if (isset($v[1]))
				{
					$default_plugin_params[$v[0]] = $v[1];
				}
			}
		}

		return $default_plugin_params;
	}

	/**
	 * Get default plugin_params j16
	 *
	 * @param   string  $plugin_name  Plugin name
	 *
	 * @return  array
	 */
	public function get_default_plugin_params_j16($plugin_name)
	{
		$query = "select params from #__extensions where element='" . $plugin_name . "' && folder='emailalerts'";
		$this->_db->setQuery($query);
		$plug_params = $this->_db->loadResult();

		if (!$plug_params)
		{
			return false;
		}

		if (preg_match_all('/\[(.*?)\]/', $plug_params, $match))
		{
			foreach ($match[1] as $mat)
			{
				$match       = str_replace(',', '|', $mat);
				$plug_params = str_replace($mat, $match, $plug_params);
			}
		}

		$newlin = explode(",", $plug_params);

		foreach ($newlin as $v)
		{
			$entry = "";

			if (!empty($v))
			{
				$v = str_replace('{', '', $v);
				$v = str_replace(':', '=', $v);
				$v = str_replace('"', '', $v);
				$v = str_replace('}', '', $v);
				$v = str_replace('[', '', $v);
				$v = str_replace(']', '', $v);
				$v = str_replace('|', ',', $v);

				$v = explode("=", $v);

				if (isset($v[1]))
				{
					$default_plugin_params[$v[0]] = $v[1];
				}
			}
		}

		return $default_plugin_params;
	}

	/**
	 * This functions returns an array of all tags from the JMA email-template with whitespace as it is
	 * For example it can detect all tags like [jma_plugin_js|cat=1,2 | sec=5,  6, 8] or [jma_plugin_js|cat=1,2]
	 *
	 * @param   string  $data  A string having user preferences as stored in email_alert table
	 *
	 * @return array $final_frontend_userdata an array of user preferences as per his ACL for enabled plugins
	 */

	public function get_frontend_plugin_data($data)
	{
		$userdata              = array();
		$newline_plugins_array = array();
		$newline_plugins_array = explode("\n", $data);

		$newline_plugins_array;

		foreach ($newline_plugins_array as $line)
		{
			if (!trim($line))
			{
				continue;
			}

			$pcs                               = explode('|', $line);
			$userconfig                        = explode('=', $pcs[1]);
			$userdata[$pcs[0]][$userconfig[0]] = $userconfig[1];
		}

		// @var_dump($userdata);die;
		$user = JFactory::getUser();

		if (JVERSION < '1.6.0')
		{
			if ($user->id)
			{
				$access = $user->aid;
			}

			$query = "SELECT element FROM #__plugins
			WHERE folder='emailalerts' AND published=1 AND access <=" . (int) $access;
		}
		else
		{
			// @TODO acl is remaining for 1.6 onwards
			$query = "SELECT element FROM #__extensions
			WHERE type='plugin' AND folder='emailalerts' AND enabled=1";

			// AND access <=".(int)$access;
		}

		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();

		$temp_data               = array();
		$final_frontend_userdata = array();

		if ($data)
		{
			foreach ($data as $d)
			{
				if ($userdata)
				{
					if (!array_key_exists($d->element, $userdata))
					{
						if (JVERSION < '1.6.0')
						{
							$p = $this->get_default_plugin_params_j15($d->element);

							$p['checked'] = "''";

							// @print_r($p);
							if (!(isset($p['is_special']) && $p['is_special']))
							{
								$temp_data[$d->element] = $p;
							}
						}
						else
						{
							$p            = $this->get_default_plugin_params_j16($d->element);
							$p['checked'] = "''";

							if (!(isset($p['is_special']) && $p['is_special']))
							{
								$temp_data[$d->element] = $p;
							}
						}
					}

					// End if array_key_exists
					else
					{
						// If key exists
						if (JVERSION < '1.6.0')
						{
							$p = $this->get_default_plugin_params_j15($d->element);

							if (!(isset($p['is_special']) && $p['is_special']))
							{
								$temp_data[$d->element] = $userdata[$d->element];
							}
						}
						else
						{
							$p = $this->get_default_plugin_params_j16($d->element);

							if (!(isset($p['is_special']) && $p['is_special']))
							{
								$temp_data[$d->element] = $userdata[$d->element];
							}
						}
					}
				}

				// End of userdara
				// If not userdata
				else
				{
					if (JVERSION < '1.6.0')
					{
						$p            = $this->get_default_plugin_params_j15($d->element);
						$p['checked'] = "''";

						if (!(isset($p['is_special']) && $p['is_special']))
						{
							$temp_data[$d->element] = $p;
						}
					}
					// @TODO needs testing
					else
					{
						$p            = $this->get_default_plugin_params_j16($d->element);
						$p['checked'] = "''";

						if (!(isset($p['is_special']) && $p['is_special']))
						{
							$temp_data[$d->element] = $p;
						}
					}
				}
				// End else if no userdata
			}

			// Code below is added to show plugins at frontend according to ACL of corresponding user

			// $data has appropriate plugin names as per users' ACL
			// $final_frontend_userdata=array();
			foreach ($data as $d)
			{
				if (array_key_exists($d->element, $temp_data))
				{
					$final_frontend_userdata[$d->element] = $temp_data[$d->element];
				}
			}
		}

		return $final_frontend_userdata;
	}

	/**
	 *This functions returns an array of all tags from the JMA email-template with whitespace as it is
	 *For example it can detect all tags like [jma_plugin_js|cat=1,2 | sec=5,  6, 8] or [jma_plugin_js|cat=1,2]
	 *
	 * @param   string  $msg_body   This is a JMA email template string
	 * @param   array   $skip_tags  This array contains all tags that are not related to any JMA plugin
	 *
	 * @return array $remember_tags an array of all detected tags
	 */
	public function get_original_tmpl_tags($msg_body, $skip_tags)
	{
		// Pattern for finding all tags with pipe EXAMPLE [jma_plug |cat=1,2| sec=5,  6, 8]
		$pattern = '/\[[A-Za-z_|=, ][A-Za-z_|=0-9, ]*\]/';
		preg_match_all($pattern, $msg_body, $matches);
		$count         = 0;
		$remember_tags = array();

		foreach ($matches[0] as $match)
		{
			if (!in_array($match, $skip_tags))
			{
				$remember_tags[$count] = $match;
				$count++;
			}
		}

		return $remember_tags;
	}

	/**
	 *This functions returns an array of all tags from the JMA email-template removing whitespace from tag names
	 *For example it can detect all tags like [jma_plugin_js|cat=1,2 | sec=5,  6, 8] or [jma_plugin_js|cat=1,2]
	 *
	 * @param   string  $msg_body   This is a JMA email template string
	 * @param   array   $skip_tags  This array contains all tags that are not related to any JMA plugin
	 *
	 * @return array $final_tmpl_tags an array of all detected tags
	 */
	public function get_tmpl_tags($msg_body, $skip_tags)
	{
		// Pattern find all tags with pipe EXAMPLE [jma_plug |cat=1,2| sec=5,  6, 8]
		$pattern = '/\[[A-Za-z_|=, ][A-Za-z_|=0-9, ]*\]/';
		preg_match_all($pattern, $msg_body, $matches);
		$tmpl_tags[] = array();
		$count       = 0;

		foreach ($matches[0] as $match)
		{
			// Remove whitespace from a tag name
			$tag = preg_replace('/\s+/', '', $match);

			if (!in_array($match, $skip_tags))
			{
				$tmpl_tags[$count] = $tag;
				$count++;
			}
		}

		$tag_counter     = 0;
		$final_tmpl_tags = array();

		foreach ($tmpl_tags as $tmpl_tag)
		{
			// Important
			$tag_to_replace = $tmpl_tag;

			// Remove square brackets [] from tags like [jma_news|count=6]
			$tmpl_tag       = preg_replace('/(\[)|(\])/', '', $tmpl_tag);

			// Create array from strings like jma_news|count=6|sec=1,3,4
			$tag            = explode('|', $tmpl_tag);

			// It's a data tag
			if (count($tag) > 1)
			{
				// The first(actually 0th) element of array is the name of the plugin
				// We need to make an array with first element as plugin name AND paramaters as other array elements
				$temp_params = array();

				// Start processing all params for a single plugin
				for ($count = 0; $count < count($tag); $count++)
				{
					// Create array from strings like catid=1,2,3
					$single_param_array = explode('=', $tag[$count]);

					// @TODO this for is unused

					// @for($ic=1;$ic<count($tag);$ic++) //$ic count is used to process $single_param_array
					// {

					// Example catid=1,2,3
					if (count($single_param_array) > 1)
					{
						$temp_params[$tag_counter][$single_param_array[0]] = $single_param_array[1];
					}
					// Example jma_latest_news
					else
					{
						$temp_params[$tag_counter]['plug_trigger'] = $single_param_array[0];
					}

					// }
				}
				// End of proceessing all params for a single tag

				$temp_params[$tag_counter]['tag_to_replace'] = $tag_to_replace;
				$final_tmpl_tags[$tag_counter]               = $temp_params[$tag_counter];
			}
			// End of if it is a data tag
			// It is a normal tag
			else
			{
				$temp_params = array();

				for ($count = 0; $count < count($tag); $count++)
				{
					// Create array from strings like count=6
					$single_param_array = explode('=', $tag[$count]);

					// @TODO this for is unused

					// @for($ic=0;$ic<count($tag);$ic++)
					// {

					// Example catid=1,2,3
					if (count($single_param_array) > 1)
					{
						$temp_params[$tag_counter][$single_param_array[0]] = $single_param_array[1];
					}
					// Example jma_latest_news
					else
					{
						$temp_params[$tag_counter]['plug_trigger'] = $single_param_array[0];
					}

					// }
				}

				$temp_params[$tag_counter]['tag_to_replace'] = $tag_to_replace;
				$final_tmpl_tags[$tag_counter]               = $temp_params[$tag_counter];
			}

			$tag_counter++;
		}
		// End of foreach

		return $final_tmpl_tags;
	}

	/**
	 *This functions returns an array of all tags each corresponding to one JMA plugin trigger
	 *
	 * @param   array  $tmpl_tags  It is an array having all tags from email template along with corresponding paramters
	 * @param   array  $user_plug  It is an array having all tags from user preferences along with corresponding paramters
	 * @param   int    $uid        The user id for user to whom mail will be sent. Needed for ACL
	 *
	 * @return array $final_trigger_tags array of all tags each corresponding to one JMA plugin trigger
	 */
	public function get_final_trigger_tags($tmpl_tags, $user_plug, $uid)
	{
		$jmailalertsModelEmails = new jmailalertsModelEmails;

		if (JVERSION < '1.6.0')
		{
			$acl = JFactory::getACL();
			$grp = $acl->getAroGroup($uid);

			if ($acl->is_group_child_of($grp->name, 'Registered') || $acl->is_group_child_of($grp->name, 'Public Backend'))
			{
				$aid = 2;
			}
			else
			{
				$aid = 1;
			}

			// Only enabled plugins should be processed as per user's acl
			$query = "SELECT element FROM #__plugins
					WHERE folder='emailalerts' AND published=1
					AND access<=" . (int) $aid;
			$this->_db->setQuery($query);
		}
		else
		{
			// @TODO aid is remaining for 1.6
			$this->_db->setQuery("SELECT element FROM #__extensions WHERE folder='emailalerts' AND enabled = 1");
		}

		$enabled_plugins = $this->_db->loadColumn();

		$i = 0;

		foreach ($tmpl_tags as $tt)
		{
			// Actual plugin name
			if (isset($tt['plug_trigger']))
			{
				$tags[$i][0] = $tt['plug_trigger'];
			}
			else
			{
				$tags[$i][0] = '';
			}

			// Actual tag/data tag  in email template.
			// This is needed when replacing tags in email with data outputed by corresponding plugin
			$tags[$i][1] = $tt['tag_to_replace'];

			$i++;
		}

		$final_trigger_tags = array();
		$tag_counter        = 0;

		foreach ($tags as $tag)
		{
			// If plugin is enabled
			if (in_array($tag[0], $enabled_plugins))
			{
				// This foreach is needed
				// Because user preferences array will be having only one instance of a one plugin

				// But in template we may use same tag 3-4 times as a data tag
				// So we need to process each data tag against all user plugins(actually matching corresponding plugin)
				foreach ($user_plug as $u)
				{
					if ($tag[0] == $u['plug_trigger'])
					{
						$single_plugin_params             = $jmailalertsModelEmails->get_single_plugin_params($tmpl_tags[$tag_counter], $u);
						$final_trigger_tags[$tag_counter] = $single_plugin_params;
					}
					elseif (isset($tmpl_tags[$tag[0]]) && isset($user_plug[$tag[0]]))
					{
						$final_trigger_tags[$tag_counter] = $single_plugin_params;
					}
				}
			}

			$final_trigger_tags[$tag_counter]['tag_to_replace'] = $tag[1];

			if (isset($final_trigger_tags[$tag_counter]['tag_to_replace']) && !isset($final_trigger_tags[$tag_counter]['plug_trigger']))
			{
				if (JVERSION < '1.6.0')
				{
					$dp = $jmailalertsModelEmails->get_default_plugin_params_j15($tag[0]);
				}
				else
				{
					$dp = $jmailalertsModelEmails->get_default_plugin_params_j16($tag[0]);
				}

				if (isset($dp['is_special']) && ($dp['is_special']))
				{
					$single_plugin_params                               = $jmailalertsModelEmails->get_single_plugin_params($tmpl_tags[$tag_counter], $dp);
					$final_trigger_tags[$tag_counter]                   = $single_plugin_params;
					$final_trigger_tags[$tag_counter]['plug_trigger']   = $tag[0];
					$final_trigger_tags[$tag_counter]['tag_to_replace'] = $tag[1];
				}
			}

			$tag_counter++;
		}

		return $final_trigger_tags;
	}

	/**
	 *Compare a single "template tag array" with corresponding "user tag array"
	 *and return a new "tag array" preserving all array indices(actually plugin parameters) for both arrays
	 *See example given below
	 *
	 * @param   array  $tmpl_tag  Template tags
	 * @param   array  $user_tag  User tags
	 *
	 * @return array $new_final_tag
	 */
	public function get_single_plugin_params($tmpl_tag, $user_tag)
	{
		$jmailalertsModelEmails = new jmailalertsModelEmails;

		if (!isset($user_tag))
		{
			$user_tag = array();
		}

		$new_final_tag = array();
		$merged        = array_merge($tmpl_tag, $user_tag);

		// Get all parameter names
		$params        = array_keys($merged);

		// Process each parameter
		foreach ($params as $param)
		{
			// If a parameter is specified in a template tag(i.e. data tag)v
			if (isset($tmpl_tag[$param]) && isset($user_tag[$param]))
			{
				$p                     = $jmailalertsModelEmails->get_single_param($tmpl_tag[$param], $user_tag[$param]);
				$new_final_tag[$param] = $p;

				if ($p)
				{
					// If common values found
					$new_final_tag[$param] = $p;
				}
				else
				{
					// If nothing is common , respect template parameter
					// @TODO might need to check
					$new_final_tag[$param] = $tmpl_tag[$param];
				}

				// @TODO important to preseve count preference set by user.
				// Need to remove this option from user preferences for every plugin
				if ($param == 'no_of_users' || $param == 'count')
				{
					$new_final_tag[$param] = $user_tag[$param];
				}
			}
			// Preserve paramters not specified in data tags but are there in user preferences
			else
			{
				if (isset($user_tag[$param]))
				{
					$new_final_tag[$param] = $user_tag[$param];
				}
			}
		}

		return $new_final_tag;
	}

	/**
	 *Compares "template tag-paramter value" and "user tag-paramater value" and returns common values(intersection of both)
	 *For example $p1=1,3,5; $p2=1,2,3,4,6,7; it should return $p3=1,3;
	 *
	 * @param   string  $tmpl_tag_param_value  string like "1,3,5"
	 * @param   string  $user_tag_param_value  string like "1,2,3,4,6,7"
	 *
	 * @return array $common_param_value "1,3"
	 */
	public function get_single_param($tmpl_tag_param_value, $user_tag_param_value)
	{
		$jmailalertsModelEmails = new jmailalertsModelEmails;
		$tmpl_param_val         = $jmailalertsModelEmails->get_exploded($tmpl_tag_param_value);
		$user_param_val         = $jmailalertsModelEmails->get_exploded($user_tag_param_value);
		$common_param_value     = array_intersect($tmpl_param_val, $user_param_val);
		$common_param_value     = implode(",", $common_param_value);

		return $common_param_value;
	}

	/**
	 * converts "1,2,3" like strings into an array
	 *
	 * @param   string  $str  string like "1,2,3,4" or "1"
	 *
	 * @return array $pieces
	 */
	public function get_exploded($str)
	{
		$pieces = explode(",", $str);

		return $pieces;
	}

	/*
	 * function is for to see whether alert has the setting to allow user to select plugin
	 * function returns 1 or 0
	 *
	 */
	/*	function allow_user_select_plugin($altid)
	{
	$query="SELECT allow_users_select_plugins
	FROM #__jma_alerts
	WHERE id=".$altid;
	$this->_db->setQuery($query);
	$allow_user_select_plugin = $this->_db->loadResult();
	return $allow_user_select_plugin;
	}*/
}
