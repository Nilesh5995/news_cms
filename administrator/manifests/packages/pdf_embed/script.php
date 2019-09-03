<?php
/**
 * @package    Pkg_Pdf_Embed
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2018 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') || die('Access denied');

/**
 * Plugin class for installation script.
 *
 * @package  Pkg_Pdf_Embed
 *
 * @since    2.1.8
 */
class Pkg_Pdf_EmbedInstallerScript
{
	/**
	 * Function to post flight
	 *
	 * @param   STRING  $type    type
	 *
	 * @param   ARRAY   $parent  parent
	 *
	 * @return  boolean true
	 *
	 * @since   2.1.8
	 *
	 */
	public function postflight($type, $parent)
	{
		// Enable plugin when installed
		if ($type == 'install')
		{
			$this->_enablePlugin('pdf_embed', 'content');
			$this->_enablePlugin('pdf_btn', 'editors-xtd');
		}

		return true;
	}

	/**
	 * Function enable plugin
	 *
	 * @param   STRING  $pluginName  plugin name
	 *
	 * @param   STRING  $pluginType  plugin name
	 *
	 * @return  boolean true
	 *
	 * @since   2.1.8
	 *
	 */
	public function _enablePlugin($pluginName, $pluginType)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Fields to update.
		$fields = array(
			$db->quoteName('enabled') . ' = ' . 1
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('element') . ' = ' . $db->quote($pluginName),
			$db->quoteName('type') . ' = ' . $db->quote('plugin'),
			$db->quoteName('folder') . ' = ' . $db->quote($pluginType),

		);

		$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->query();

		return true;
	}
}
