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

/**
 * Simulate model class
 *
 * @package  JMailAlerts
 *
 * @since    2.5.0
 */
class JMailalertsModelMailSimulate extends JModelLegacy
{
	/**
	 * Send simulated email
	 *
	 * @return  int
	 *
	 * @since  2.5.0
	 */
	public function simulate()
	{
		jimport('joomla.filesystem.file');

		$input = JFactory::getApplication()->input;

		require_once JPATH_SITE . '/components/com_jmailalerts/models/emails.php';
		require_once JPATH_SITE . '/components/com_jmailalerts/helpers/emailhelper.php';

		$jmailalertsemailhelper = new jmailalertsemailhelper;
		$jmailalertsModelEmails = new jmailalertsModelEmails;

		$nofrm = 0;

		// $today=date('Y-m-d H:i:s');  previous code

		// Get date selected in simulate
		$today                     = $input->get('select_date_box', '', 'STRING');
		$target_user_id            = $input->get('user_id_box', '', 'INT');
		$alert_type_id             = $input->get('altypename', '', 'INT');
		$destination_email_address = $input->get('send_mail_to_box', '', 'STRING');
		$flag                      = $input->get('flag', '', 'INT');
		$email_status = 0;

		if (!$alert_type_id || !$target_user_id || !$destination_email_address)
		{
			return 2;
		}

		$query = "SELECT u.id as user_id, u.name, u.email as email_id,
		 a.template, a.email_subject,
		 e.date, e.alert_id,
		 a.template_css,
		 e.plugins_subscribed_to,
		 a.respect_last_email_date
		 FROM #__users AS u,
		 #__jma_subscribers AS e,
		 #__jma_alerts AS a
		 WHERE e.user_id = " . $target_user_id . "
		 AND e.alert_id = " . $alert_type_id . "
		 AND u.id = e.user_id
		 AND a.id = e.alert_id";

		$this->_db->setQuery($query);
		$target_user_data = $this->_db->loadObjectList();

		$i = 0;

		foreach ($target_user_data as $data)
		{
			if ($data->date)
			{
				// $data[$i]->date = $today;
				$data->date = ($today) ? $today:$data->date;
			}
			else
			{
				$data[$i]->date = ($today) ? $today:$data[$i]->date;
			}

			$i++;
		}

		if ($target_user_data)
		{
			// @echo $destination_email_address;
			$target_user_data[0]->email_id = $destination_email_address;

			// Get template from alert type
			$query = "SELECT template FROM #__jma_alerts WHERE id =" . $alert_type_id;
			$this->_db->setQuery($query);
			$msg_body = $this->_db->loadResult();

			$skip_tags     = array('[SITENAME]','[NAME]','[SITELINK]','[PREFRENCES]', '[mailuser]');
			$tmpl_tags     = $jmailalertsModelEmails->get_tmpl_tags($msg_body, $skip_tags);
			$remember_tags = $jmailalertsModelEmails->get_original_tmpl_tags($msg_body, $skip_tags);

			$response = $jmailalertsemailhelper->getMailcontent($target_user_data[0], $flag, $tmpl_tags, $remember_tags);

			if (isset($response))
			{
				return $response[1];
			}
		}
		else
		{
			return 2;
		}
	}

	/**
	 * Function to call plugins and return the output. This function is called from the addtomailq() function above
	 *
	 * @param   int     $id    User id
	 * @param   string  $date  Date
	 *
	 * @return  array
	 *
	 * @since   2.5.0
	 */
	public function getPlugins($id, $date)
	{
		JPluginHelper::importPlugin('emailalerts');

		$dispatcher = JDispatcher::getInstance();
		$results    = $dispatcher->trigger('onBeforeAlertEmail', array($id, $date));

		return $results;
	}

	/**
	 * Get published alerts
	 *
	 * @return  string
	 *
	 * @since  2.5.0
	 */
	public function getAlertypename()
	{
		$db	= JFactory::getDBO();
		$db->setQuery("SELECT id  AS val, title AS text FROM #__jma_alerts WHERE state=1");
		$altypename	= $db->loadObjectList();

		return JHtml::_('select.genericList', $altypename, 'altypename', 'class="inputbox"', 'val', 'text', '');
	}
}
