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
 * class will contain function to store alerts records
 *
 * @package     Com_Jmailaalerts
 * @subpackage  site
 * @since       1.0
 */
class Jmailalertsemailhelper
{
	/**
	 * Function to actually send the content of the mail
	 * It is called by the function processMailAlerts() above and also from the backend model simulate
	 *
	 * @param   ARRAY    $userdata       user data
	 * @param   integer  $flag           flag
	 * @param   array    $tmpl_tags      tags
	 * @param   integer  $remember_tags  remember tags
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function getMailcontent($userdata, $flag, $tmpl_tags, $remember_tags)
	{
		$log = array();
		$jmailalertsModelEmails = new jmailalertsModelEmails;
		$db = JFactory::getDBO();
		$helperPath = JPATH_SITE . '/components/com_jmailalerts/models/emails.php';

		if (!class_exists('jmailalertsModelEmails'))
		{
			//  require_once $path;
			JLoader::register('jmailalertsModelEmails', $helperPath);
			JLoader::load('jmailalertsModelEmails');
		}

		$params = JComponentHelper::getParams('com_jmailalerts');

		//  require(JPATH_SITE . "/components/com_jmailalerts/emails/config.php");

		$today = JFactory::getDate();

		if (!empty($userdata))
		{
			$app = JFactory::getApplication();
			$frommail = $app->getCfg('mailfrom');
			$site = $app->getCfg('sitename');

			// $message_body=stripslashes($params->get('message_body'));
			$message_body = stripslashes($userdata->template);

			// $message_subject=stripslashes($params->get('message_subject'));
			$message_subject = stripslashes($userdata->email_subject);
			$emailofuser = trim($userdata->email_id);

			$user_plug = $jmailalertsModelEmails->getUserPlugData($userdata->plugins_subscribed_to);
			$final_trigger_tags = $jmailalertsModelEmails->get_final_trigger_tags($tmpl_tags, $user_plug, $userdata->user_id);
			$count = 0;

			foreach ($final_trigger_tags as $ftt)
			{
				if (isset($ftt['plug_trigger']))
				{
					// $user_subscribed_array[$count][0]=$ftt['plug_trigger'];
					$usa[$count] = $ftt['plug_trigger'];

					//  needed for log
				}

				// $user_subscribed_array[$count][1]=$ftt['tag_to_replace'];
				$count++;
			}

			/*if($params->get('enb_debug') && $flag == 2 )	//if verbose debug is ON
			{
				$this->log[] = JText::sprintf("PRO_FOR",$userdata->name,$userdata->id);
				$plug=implode(',',$usa);
				$this->log[] = JText::sprintf("APPLICABLE_PLUG",$plug);
			}*/

			//  if verbose debug is ON
			if ($params->get('enb_debug') && $flag == 2)
			{
				$log[] = "*** " . JText::sprintf("COM_JMAILALERTS_PRO_FOR", $userdata->name, $userdata->user_id);

				if (count($usa))
				{
					$plug = implode(', ', $usa);
					$log[] = JText::sprintf("COM_JMAILALERTS_APPLICABLE_PLUG", $plug);

					//  echo JText::sprintf("COM_JMAILALERTS_APPLICABLE_PLUG", $plug);
				}
				else
				{
					$log[] = JText::sprintf("COM_JMAILALERTS_APPLICABLE_PLUG", "No applicable plugin found");

					// @TODO add lang. string

					//  echo JText::sprintf("COM_JMAILALERTS_APPLICABLE_PLUG", "No applicable plugin found"); //@TODO add lang. string
				}
			}

			// $plugins_data=jmailalertsModelEmails::gettriggerPlugins($userdata->id, $userdata->date, $final_trigger_tags, $params->get('enb_latest'));

			if (isset($userdata->respect_last_email_date))
			{
				$respect_last_email_date = $userdata->respect_last_email_date;
			}
			else
			{
				$respect_last_email_date = 0;
			}

			$plugins_data = $jmailalertsModelEmails->gettriggerPlugins($userdata->user_id, $userdata->date, $final_trigger_tags, $respect_last_email_date);
			/* foreach ($plugins_data as $pd){
				$plugins_data_name[]=$pd[0];
			}*/

			// Rebuild array for tag repalcement in the same order as the plugins were triggered

			$count = 0;
			$user_subscribed_array = array();

			if ($plugins_data)
			{
				foreach ($plugins_data as $pd)
				{
					$plugins_data_name[] = $pd[0];

					if (isset($pd[0]))
					{
						//  plug_trigger
						$user_subscribed_array[$count][0] = $pd[0];
					}

					if (isset($pd[3]))
					{
						//  tag_to_replace
						$user_subscribed_array[$count][1] = $pd[3];
					}

					$count++;
				}
			}

			$sitelink = "<a href = '" . JURI::root() . "'>" . JText::_("COM_JMAILALERTS_CLICK") . "</a>";
			$pref_sitelink = '<a href="' . JURI::root() . 'index.php?option=com_jmailalerts&amp;view=emails">' . JText::_("COM_JMAILALERTS_CLICK") . '</a>';
			$find = array('[SITENAME]', '[NAME]', '[SITELINK]', '[PREFRENCES]', '[mailuser]');
			$replace = array($site, $userdata->name, $sitelink, $pref_sitelink, $emailofuser);
			$message_body = str_replace($find, $replace, $message_body);
			$message_subject = str_replace('[SITENAME]', $site, $message_subject);
			$no_mail = 0;
			$cssdata = '';
			$i = 0;

			foreach ($user_subscribed_array as $plug)
			{
				if (isset($plugins_data[$i]))
				{
					$message_body = str_replace($plug[1], $plugins_data[$i][1], $message_body);
					$cssdata     .= $plugins_data[$i][2];

					if (!($plugins_data[$i][1] == ''))
					{
						$no_mail = 1;
					}
				}

				$i++;
			}
			// Replace all tags that are not part of user preferences directly with ''

			// @TODO need to take care of when processing special plugins
			foreach ($remember_tags as $rt)
			{
				$message_body = str_replace($rt, '', $message_body);
			}

			$return = array();

			if (!($no_mail == 0))
			{
				// Separated CSS in 2.4

				// $cssfile = JPATH_SITE.DS."components".DS."com_jmailalerts".DS."emails".DS."mail_alert_style.css";

				// $cssfile = JPATH_SITE.DS."components".DS."com_jmailalerts".DS."emails".DS."default_template.css";

				// $cssdata .= JFile::read($cssfile);
				$cssdata .= $userdata->template_css;
				$common_plugin_css_file = JPATH_SITE . "/components/com_jmailalerts/assets/css/common_plugin.css";
				$cssdata .= JFile::read($common_plugin_css_file);

				$mail_data = $jmailalertsModelEmails->getEmogrify($message_body, $cssdata);

				// Flag=1 => mail simulation
				if ($flag == 1)
				{
					echo $mail_data;
					jexit();
				}

				// Send email
				$mode = 1;
				$cc = null;
				$bcc = null;
				$bcc = null;
				$attachment = null;
				$replyto = null;
				$replytoname = null;
				$status = JFactory::getMailer()->sendMail(
				$frommail, $site, $emailofuser, $message_subject, $mail_data, $mode, $cc, $bcc, $attachment, $replyto, $replytoname
				);

				// $status = JMail::sendMail($frommail, $site, $emailofuser, $message_subject, $mail_data, true); //2.4

				// Mask email in log?
				$maskEmailInLog = $params->get('mask_email_log', 1);

				if ($maskEmailInLog)
				{
					$emailofuser = $this->maskEmail($emailofuser);
				}

				if (isset($status->code) && $status->code == 500)
				{
					$log[] = $status->message . " " . JText::sprintf("COM_JMAILALERTS_MAIL_SEND_FAILED", $emailofuser, $today);
					$status = 4;

					array_push($return, $log, $status);

					return $return;
				}
				elseif ($status)
				{
					$log[] = JText::sprintf("COM_JMAILALERTS_MAIL_SEND_SUCCESS", $emailofuser, $today);

					//  flag=2 => actual sending of email

					if ($flag == 2)
					{
						$query = "UPDATE `#__jma_subscribers` SET `date` ='"
						. $today . "' WHERE `user_id` = " . $userdata->user_id . " AND alert_id = " . $userdata->alert_id;
						$db->setQuery($query);
						$db->execute();
					}

					$status = 1;
					array_push($return, $log, $status);

					return $return;
				}
			}
			else
			{
				// When there is no content to send in the mail
				//  flag=2 => actual sending of email
				if ($flag == 2)
				{
					$query = "UPDATE `#__jma_subscribers` SET `date` ='"
					. $today . "' WHERE `user_id` = " . $userdata->user_id . " AND alert_id = " . $userdata->alert_id;
					$db->setQuery($query);
					$db->execute();
				}

				$status = 3;
				array_push($return, $log, $status);

				return $return;
			}
		}
	}

	/**
	 * Mask email
	 * This replaces some of the characters from email address with * <br/>Eg. user1@mail.com will be logged as us***@**il.com
	 * https://stackoverflow.com/a/42877897/1143337
	 *
	 * @param   string  $email  Email address
	 *
	 * @return  array
	 */
	public function maskEmail($email)
	{
		$mail_parts = explode("@", $email);
		$length     = strlen($mail_parts[0]);

		if ($length <= 4 && $length > 1)
		{
			$show = 1;
		}
		else
		{
			$show = floor($length / 2);
		}

		$hide    = $length - $show;
		$replace = str_repeat("*", $hide);

		return substr_replace($mail_parts[0], $replace, $show, $hide) . "@" . substr_replace($mail_parts[1], "**", 0, 2);
	}
}
