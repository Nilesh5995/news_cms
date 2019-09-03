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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Simulate model class
 *
 * @package  JMailAlerts
 *
 * @since    2.5.0
 */
class JMailalertsModelHealthCheck extends JModelLegacy
{
	/**
	 * Get health check data
	 *
	 * @return  array
	 */
	public function healthcheck()
	{
		jimport('joomla.filesystem.file');
		$db = JFactory::getDBO();
		$data = array();

		$data['installed'] = $this->installedPlugins($db);
		$data['enable'] = $this->enabledPlugins($db);
		$data['plgname'] = $this->getPlugnames($db);
		$data['alerts'] = $this->createdAlert($db);
		$data['published'] = $this->publishedAlert($db);
		$data['defaults'] = $this->defoultAlert($db);
		$data['synced'] = $this->syncedAlert($db);

		return $data;
	}

	/**
	 * Get installed jmailalerts plugin count
	 *
	 * @param   object  $db  Joomla JDbo
	 *
	 * @return  array
	 */
	public function installedPlugins($db)
	{
		$this->_db->setQuery('SELECT COUNT(e.extension_id)  AS number FROM #__extensions AS e WHERE folder=\'emailalerts\' ');
		$installplg = $this->_db->loadResult();

		return $installplg;
	}

	/**
	 * Get enabled jmailalerts plugin count
	 *
	 * @param   object  $db  Joomla JDbo
	 *
	 * @return  array
	 */
	public function enabledPlugins($db)
	{
		$this->_db->setQuery('SELECT COUNT(e.extension_id) AS number FROM #__extensions AS e WHERE folder=\'emailalerts\' AND e.enabled = \'1\' ');
		$enableplg = $this->_db->loadResult();

		return $enableplg;
	}

	/**
	 * Get alerts count
	 *
	 * @param   object  $db  Joomla JDbo
	 *
	 * @return  array
	 */
	public function createdAlert($db)
	{
		$db->setQuery("SELECT COUNT(al.id) AS number FROM #__jma_alerts AS al ");
		$created = $db->loadResult();

		return $created;
	}

	/**
	 * Get Plug names
	 *
	 * @return  obect|string
	 */
	public function getPlugnames()
	{
		$this->_db->setQuery('SELECT name, enabled, element FROM #__extensions WHERE folder=\'emailalerts\' ORDER BY element');
		$plugname = $this->_db->loadObjectList();

		return $plugname =(!empty($plugname)) ? $plugname : JText::_('NO_PLUGINS_ENABLED_OR_INSTALLED');
	}

	/**
	 * Get published alert count
	 *
	 * @param   object  $db  Joomla JDbo
	 *
	 * @return  array
	 */
	public function publishedAlert($db)
	{
		$db->setQuery("SELECT COUNT(al.id) AS number FROM #__jma_alerts AS al WHERE al.state = '1' ");
		$created = $db->loadResult();

		return $created;
	}

	/**
	 * Get default alerts count
	 *
	 * @param   object  $db  Joomla JDbo
	 *
	 * @return  array
	 */
	public function defoultAlert($db)
	{
		$db->setQuery("SELECT al.id FROM #__jma_alerts AS al WHERE al.is_default = 1 ");
		$default = $db->loadColumn();
		$default = (!empty($default['0'])) ? COUNT($default) : 0;

		return $default;
	}

	/**
	 * Get syned alerts count
	 *
	 * @param   object  $db  Joomla JDbo
	 *
	 * @return  array
	 */
	public function syncedAlert($db)
	{
		$db->setQuery("SELECT COUNT(DISTINCT(ea.alert_id)) AS number FROM #__jma_subscribers AS ea ");
		$synced	= $db->loadResult();

		return $synced;
	}
}
