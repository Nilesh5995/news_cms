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

/**
 * ManageUserHelper helper.
 */
class ManageUserHelper
{
	/**
	 * Method to subscribe new user when it added in manage user Subscriber
	 */
	public static function SubscribeUser($user)
	{
		$db = JFactory::getDBO();

		$userid = $user['user_id'];

		//recieve array of alert id where set to defaoult
		$query = 'SELECT id  FROM #__jma_alerts WHERE is_default = 1';
		$db->setQuery($query);
		$alertid = $db->loadColumn();
		$alertid_string = $alertid;

		$alertqry = "";
		for ($i = 0; $i < count($alertid_string); $i++)
		{
			$alertqry.="id=" . $alertid_string[$i];
			if ($i != (count($alertid_string) - 1))
				$alertqry.=" OR ";
		}

		$query = 'SELECT element FROM #__extensions WHERE folder = \'emailalerts\'  AND enabled = 1';
		$db->setQuery($query);
		$plugnamecompair = $db->loadColumn();
		$plugnamesend = implode(',', $plugnamecompair);
		$plugnamecompair = explode(',', $plugnamesend);

		$cnt = 0;
		$rnt = 99;
		$query = "SELECT id,default_freq,template FROM #__jma_alerts WHERE $alertqry";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		foreach ($result as $key)
		{
			$date = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $key->default_freq, date("Y"))); //G
			$entry = "";

			for ($i = 0; $i < count($plugnamecompair); $i++)
			{
				if (strstr($key->template, $plugnamecompair[$i]))
					$plugin_name_string[] = $plugnamecompair[$i];
			}

			foreach ($plugin_name_string as $plug)
			{
				$query = "select params from #__extensions where element='" . $plug . "' && folder='emailalerts'";
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
						$v = str_replace(':', '=', $v);
						$v = str_replace('"', '', $v);
						$v = str_replace('}', '', $v);
						$v = str_replace('[', '', $v);
						$v = str_replace(']', '', $v);
						$v = str_replace('|', ',', $v);
						/*if ($plug == 'jma_latestnews_js')
						{
							$cnt++;
						}*/
						if (!($cnt > $rnt))
							$entry.=$plug . '|' . $v . "\n";
					}
					/*if ($plug == 'jma_latestnews_js')
					{
						$entry = str_replace('category', 'catid', $entry);
						$entry = str_replace('sections', 'secid', $entry);
					}*/
				}

				$cnt = 0;
			}

			unset($plugin_name_string);
			unset($match);
			$user_data = array();

			// Plugins paramenter
			$user_data['plugins_subscribed_to'] = $entry;

			// Date of subscription -(minus) frequency
			$user_data['date'] = $date;
			return $user_data;
		}
	}
}

