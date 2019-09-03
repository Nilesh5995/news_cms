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

jimport('joomla.form.formfield');

/**
 * Custom cron field for component params.
 *
 * @since  2.5.0
 */
class JFormFieldCron extends JFormField
{
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * 
	 * @since	1.6
	 */
	public function getInput()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root(true) . '/components/com_jmailalerts/assets/css/jmailalerts.css');

		if ($this->name == 'jform[private_key_cronjob]')
		{
			return $this->getCronKey($this->name, $this->value, $this->element, $this->options['control']);
		}
		elseif ($this->name == 'jform[cron_url]')
		{
			$cronjoburl = $this->getCronUrl($this->name, $this->value, $this->element, $this->options['control']);

			return $return = '<input type="text" name="cron_url" disabled="disabled" value="' . $cronjoburl . '" class="input input-xxlarge">';
		}
	}

	/**
	 * Get Cron Key
	 *
	 * @param   string  $name          Name
	 * @param   string  $value         Value
	 * @param   object  &$node         JForm field
	 * @param   string  $control_name  Name
	 *
	 * @return  string
	 */
	public function getCronKey($name, $value, &$node, $control_name)
	{
		// Generate randome string
		if (empty($value))
		{
			$length       = 10;
			$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';

			for ($i = 0; $i < $length; $i++)
			{
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}

			return "<input type='text' name='" . $name . "' value='" . $randomString . "'>";
		}

		return "<input type='text' name='$name' value=" . $value . "></label>";
	}

	/**
	 * Get Cron URL
	 *
	 * @param   string  $name          Name
	 * @param   string  $value         Value
	 * @param   object  &$node         JForm field
	 * @param   string  $control_name  Name
	 *
	 * @return  string
	 */
	public function getCronUrl($name, $value, &$node, $control_name)
	{
		$params                    = JComponentHelper::getParams('com_jmailalerts');
		$this->private_key_cronjob = $params->get('private_key_cronjob');

		$url = JRoute::_(
				JUri::root() .
				'index.php?option=com_jmailalerts&view=emails&tmpl=component&task=processMailAlerts&pkey=' .
				$this->private_key_cronjob
		);

		return $url;
	}
}
