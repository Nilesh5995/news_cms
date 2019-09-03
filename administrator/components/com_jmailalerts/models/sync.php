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
jimport('joomla.html.parameter');
jimport('joomla.form.form');

/**
 * Model for sync
 *
 * @since  1.0.0
 */
class JmailalertsModelsync extends JModelLegacy
{
	/**
	 * Function to get the plugin data(names, elements) related to ejmailalerts
	 *
	 * @return object
	 */
	public function getPluginData()
	{
		$this->_db->setQuery("SELECT name,element FROM #__extensions WHERE enabled=1 AND folder='emailalerts'");

		return $this->_db->loadObjectList();
	}

	/**
	 * Function called from view.html.php of sync. It returns the alert name with default selected alert id
	 *
	 * @return object
	 */
	public function getAlertnames()
	{
			// Get default alert ids
			$this->_db->setQuery("SELECT alert.* FROM #__jma_alerts as alert WHERE state=1");
			$alertnames = $this->_db->loadObjectList();
			$options    = array();

			foreach ($alertnames as $alertname)
			{
				$options[] = JHtml::_('select.option', $alertname->id, $alertname->title);
			}

			return $options;
	}

	/**
	 * Get the default preferences from jmail alerts table
	 *
	 * @return object
	 */
	public function getPluginNames()
	{
		// FIRST GET THE EMAIL-ALERTS RELATED PLUGINS FRM THE `jos_plugins` TABLE
		$this->_db->setQuery('SELECT element FROM #__extensions WHERE folder = \'emailalerts\'  AND enabled = 1');

		// Get the plugin names and store in an array
		$email_alert_plugins_array = $this->_db->loadColumn();

		return  $email_alert_plugins_array;
	}

	/**
	 * Method to get the frequencies according to the atert id
	 *
	 * @param   int  $alertid  Alert id
	 *
	 * @return array
	 */
	public function getFrequencies($alertid)
	{
		$this->_db->setQuery("SELECT alert.allowed_freq,alert.default_freq
			FROM #__jma_alerts AS alert
			WHERE alert.id=" . $alertid . "
		");

		$alert_details = $this->_db->loadObject();

		$allowed_freqs = $alert_details->allowed_freq;

		// Build array to replace ["1","3"] & make 1,3
		$search        = array('[', ']', '"');
		$allowed_freqs = str_replace($search, '', $allowed_freqs);

		$this->_db->setQuery("SELECT freq.id, freq.name as freq_name
			FROM #__jma_frequencies as freq
			WHERE freq.id IN (" . $allowed_freqs . ")
		");

		if (count($this->_db->loadAssocList()))
		{
			$frequencies = array();

			foreach ($this->_db->loadAssocList() as $f)
			{
				$i = 0;

				$frequencies[$i]['id']        = $f['id'];
				$frequencies[$i]['freq_name'] = JText::_($f['freq_name']);
			}
		}

		return $frequencies;
	}

	/**
	 * Method to get the alert default freq
	 *
	 * @param   int  $alertId  Alert id
	 *
	 * @return array
	 */
	public function getDefaultFreq($alertId)
	{
		if (empty($alertId) || $alertId == 'null')
		{
			return array();
		}

		$this->_db->setQuery("SELECT alert.id as alertid,alert.default_freq,freq.name,freq.time_measure,freq.duration
			FROM #__jma_alerts as alert
			LEFT JOIN #__jma_frequencies as freq ON freq.id=alert.default_freq
			WHERE alert.id=" . $alertId . "
		");

		$alert_details = $this->_db->loadAssocList();

		if (isset($alert_details))
		{
			if ($alert_details['0']['time_measure'] == 'days')
			{
				$alert_details['0']['last_email_date'] = date(
					JText::_('COM_JMAILALERTS_DATE_FORMAT_PHP'),
					strtotime(date(JText::_('COM_JMAILALERTS_DATE_FORMAT_PHP')) . ' - ' . $alert_details['0']['duration'] . ' days')
				);
			}
			else
			{
				$alert_details['0']['last_email_date'] = date(
					JText::_('COM_JMAILALERTS_DATE_FORMAT_PHP'),
					strtotime(date(JText::_('COM_JMAILALERTS_DATE_FORMAT_PHP')) . '- 1 days')
				);
			}

			$alert_details['0']['name'] = JText::_($alert_details['0']['name']);
		}

		return $alert_details;
	}
}
