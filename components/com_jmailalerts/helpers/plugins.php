<?php
/**
 * @package     JMailAlerts
 * @subpackage  com_jmailalerts
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

// Do not allow direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

if (!class_exists('pluginHelper'))
{
	/**
	 * Class for plugins helper
	 *
	 * @package  JMailAlerts
	 * @since    2.5
	 */
	class PluginHelper
	{
		/**
		 * Gets the parsed layout file
		 *
		 * @param   string  $layout         The name of  the layout file
		 * @param   object  $vars           Variables to assign to
		 * @param   string  $plugin_params  Plugin params
		 * @param   string  $plugin         The name of the plugin
		 * @param   string  $group          The plugin's group
		 *
		 * @return  string
		 */
		public function getLayout($layout, $vars = false, $plugin_params, $plugin = '', $group = 'emailalerts')
		{
			$plugin = $layout;
			ob_start();
			$layout = $this->getLayoutPath($plugin, $group, $layout, $plugin_params);
			include $layout;
			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}

		/**
		 * Get the path to a layout file
		 *
		 * @param   string  $plugin  The name of the plugin file
		 * @param   string  $group   The plugin's group
		 * @param   string  $layout  The name of the plugin layout file
		 *
		 * @return  string  The path to the plugin layout file
		 */
		public function getLayoutPath($plugin, $group, $layout = 'default')
		{
			$app = JFactory::getApplication();

			// Get the template and default paths for the layout
			$templatePath = JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $group . '/' . $plugin . '/' . $layout . '.php';

			$defaultPath = JPATH_SITE . '/plugins/' . $group . '/' . $plugin . '/' . $plugin . '/tmpl/' . $layout . '.php';

			// If the site template has a layout override, use it
			jimport('joomla.filesystem.file');

			if (JFile::exists($templatePath))
			{
				return $templatePath;
			}
			else
			{
				return $defaultPath;
			}
		}

		/**
		 * Get the path to css file
		 *
		 * @param   string  $layout         The name of the plugin layout file
		 * @param   string  $plugin_params  Plugin params
		 *
		 * @return  string  The path to the plugin css file
		 */
		public function getCSSLayoutPath($layout = 'default', $plugin_params)
		{
			$plugin = $layout;
			$group  = 'emailalerts';
			$app    = JFactory::getApplication();

			// Get the template and default paths for the layout
			$templatePath = JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $group . '/' . $plugin . '/' . $layout . '.css';

			$defaultPath = JPATH_SITE . '/plugins/' . $group . '/' . $plugin . '/' . $plugin . '/tmpl/' . $layout . '.css';

			// If the site template has a layout override, use it
			jimport('joomla.filesystem.file');

			if (JFile::exists($templatePath))
			{
				return $templatePath;
			}
			else
			{
				return $defaultPath;
			}
		}

		/**
		 * Get itemid for given link
		 *
		 * @param   string   $link          link
		 * @param   integer  $skipIfNoMenu  Decide to use Itemid from $input
		 *
		 * @return  integer
		 *
		 * @since  3.0
		 */
		public function getItemId($link, $skipIfNoMenu = 0)
		{
			$itemid    = 0;
			$mainframe = JFactory::getApplication();
			$input     = JFactory::getApplication()->input;

			if ($mainframe->issite())
			{
				$JSite = new JSite;
				$menu  = $JSite->getMenu();
				$items = $menu->getItems('link', $link);

				if (isset($items[0]))
				{
					$itemid = $items[0]->id;
				}
			}

			if (!$itemid)
			{
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query
					->select('id')
					->from('#__menu')
					->where('link LIKE "%' . $link . '%"')
					->where('published = 1')
					->where('client_id = 0')
					->setLimit('1');

				$db->setQuery($query);
				$itemid = $db->loadResult();
			}

			if (!$itemid)
			{
				if ($skipIfNoMenu)
				{
					$itemid = 0;
				}
				else
				{
					$itemid  = $input->get->get('Itemid', '0', 'INT');
				}
			}

			return $itemid;
		}

		/**
		 * Sorts a multidimentional array as per given column
		 *
		 * @param   array   $array   Array of nodes
		 * @param   string  $column  Column based on which sorting will be done
		 * @param   string  $order   Sorting order direction 0(ASC) or 1(DESC)
		 *
		 * @return  array
		 *
		 * @since    3.0
		 */
		public function multi_d_sort($array, $column, $order)
		{
			if (isset($array) && count($array))
			{
				foreach ($array as $key => $row)
				{
					$orderby[$key] = $row->$column;
				}

				if ($order)
				{
					array_multisort($orderby, SORT_DESC, $array);
				}
				else
				{
					array_multisort($orderby, SORT_ASC, $array);
				}
			}

			return $array;
		}
	}
}

if (!class_exists('JMailAlertsPlugin'))
{
	/**
	 * Class for plugins helper
	 *
	 * @package  JMailAlerts
	 *
	 * @since    2.5
	 */
	class JMailAlertsPlugin extends JPlugin
	{
		/**
		 * Affects constructor behavior. If true, language files will be loaded automatically.
		 *
		 * @var    boolean
		 * @since  2.6.0
		 */
		protected $autoloadLanguage = true;

		public $parentExtensionExists = false;

		public $returnArray;

		/**
		 * Constructor
		 *
		 * @param   object  &$subject  subject
		 *
		 * @param   array   $config    plugin config
		 *
		 * @since   2.5.1
		 */
		public function __construct(& $subject, $config)
		{
			parent::__construct($subject, $config);

			// Set array for data to be returned
			// 0, 1, 2 : plugin name, plugin HTML, plugin CSS
			$this->returnArray    = array();
			$this->returnArray[0] = $this->_name;
			$this->returnArray[1] = '';
			$this->returnArray[2] = '';

			// Parent extension installed or not check
			$this->checkExtensionExists($this->extension);

			// If related extension not installed, return
			if (!$this->parentExtensionExists)
			{
				return false;
			}

			// Set plugin params
			if ($this->params === false)
			{
				$jPlugin      = JPluginHelper::getPlugin('emailalerts', $this->_name);
				$this->params = new JParameter($jPlugin->params);
			}

			// Load language file for plugin frontend
			$lang = JFactory::getLanguage();
			$lang->load('plg_emailalerts_' . $this->_name, JPATH_ADMINISTRATOR);

			$this->loadLanguage();
		}

		/**
		 * Check if extension is installed
		 *
		 * @param   string  $extension  Extension name
		 *
		 * @since   2.6.0
		 *
		 * @return  boolean
		 */
		public function checkExtensionExists($extension)
		{
			$extPath = JPATH_ROOT . '/components/' . $extension;

			if (JFolder::exists($extPath))
			{
				$this->parentExtensionExists = true;
			}
			else
			{
				$this->parentExtensionExists = false;
			}
		}

		/**
		 * Plugin trigger to get latest matching records
		 *
		 * @param   string  $id               Userid or email id for user whom email will be sent
		 * @param   string  $lastEmailDate    Timestamp when last email was sent to that user
		 * @param   array   $userParams       Array of user's alert preference considering data tags
		 * @param   int     $fetchOnlyLatest  Decide to send only fresh content or not
		 *
		 * @return  array
		 *
		 * @since  2.5.0
		 */
		public function onEmailTrigger($id, $lastEmailDate, $userParams, $fetchOnlyLatest)
		{
			// If related extension not installed, return
			if (!$this->parentExtensionExists)
			{
				return $this->returnArray;
			}

			$result = $this->getList($id, $lastEmailDate, $userParams, $fetchOnlyLatest);

			// Set plugin HTML, CSS
			if (!empty($result))
			{
				$this->setPluginHTML($result);
				$this->setPluginCSS();
			}

			return $this->returnArray;
		}

		/**
		 * Sets plugin HTML o/p in return array
		 *
		 * @param   array  $list  Variables to assign to
		 *
		 * @return  void
		 */
		public function setPluginHTML($list = false)
		{
			$plugin = $this->_name;
			$layout = $this->_name;

			ob_start();
			$layoutPath    = $this->getLayoutPath($plugin, $layout);
			$pluginParams  = $this->params;
			include $layoutPath;
			$html          = ob_get_contents();
			ob_end_clean();

			// Set HTML into return variable
			$this->returnArray[1] = $html;
		}

		/**
		 * Get the path to a layout file
		 *
		 * @param   string  $plugin  The name of the plugin file
		 * @param   string  $layout  The name of the plugin layout file
		 *
		 * @return  string  The path to the plugin layout file
		 */
		public function getLayoutPath($plugin, $layout = 'default')
		{
			$app   = JFactory::getApplication();
			$group = 'emailalerts';

			// Get the template and default paths for the layout
			$templatePath = JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $group . '/' . $plugin . '/' . $layout . '.php';

			$defaultPath = JPATH_SITE . '/plugins/' . $group . '/' . $plugin . '/' . $plugin . '/tmpl/' . $layout . '.php';

			// If the site template has a layout override, use it
			jimport('joomla.filesystem.file');

			if (JFile::exists($templatePath))
			{
				return $templatePath;
			}
			else
			{
				return $defaultPath;
			}
		}

		/**
		 * Sets plugin CSS o/p in return array
		 *
		 * @return  void
		 */
		public function setPluginCSS()
		{
			$plugin = $this->_name;
			$layout = $this->_name;
			$group  = 'emailalerts';
			$app    = JFactory::getApplication();

			// Get the template and default paths for the layout
			$templateCssPath = JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $group . '/' . $plugin . '/' . $layout . '.css';

			$defaultCssPath = JPATH_SITE . '/plugins/' . $group . '/' . $plugin . '/' . $plugin . '/tmpl/' . $layout . '.css';

			// If the site template has a layout override, use it
			jimport('joomla.filesystem.file');

			if (JFile::exists($templateCssPath))
			{
				$css = file_get_contents($templateCssPath);
			}
			else
			{
				$css = file_get_contents($defaultCssPath);
			}

			// Set CSS into return variable
			$this->returnArray[2] = $css;
		}

		/**
		 * Get itemid for given link
		 *
		 * @param   string   $link          link
		 * @param   integer  $skipIfNoMenu  Decide to use Itemid from $input
		 *
		 * @return  integer
		 *
		 * @since  3.0
		 */
		public function getItemId($link, $skipIfNoMenu = 0)
		{
			$itemid    = 0;
			$mainframe = JFactory::getApplication();
			$input     = JFactory::getApplication()->input;

			if ($mainframe->issite())
			{
				$JSite = new JSite;
				$menu  = $JSite->getMenu();
				$items = $menu->getItems('link', $link);

				if (isset($items[0]))
				{
					$itemid = $items[0]->id;
				}
			}

			if (!$itemid)
			{
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query
					->select('id')
					->from('#__menu')
					->where('link LIKE "%' . $link . '%"')
					->where('published = 1')
					->where('client_id = 0')
					->setLimit('1');

				$db->setQuery($query);
				$itemid = $db->loadResult();
			}

			if (!$itemid)
			{
				if ($skipIfNoMenu)
				{
					$itemid = 0;
				}
				else
				{
					$itemid  = $input->get->get('Itemid', '0', 'INT');
				}
			}

			return $itemid;
		}

		/**
		 * Sorts a multidimentional array as per given column
		 *
		 * @param   array   $array   Array of nodes
		 * @param   string  $column  Column based on which sorting will be done
		 * @param   string  $order   Sorting order direction 0(ASC) or 1(DESC)
		 *
		 * @return  array
		 *
		 * @since    3.0
		 */
		public function multi_d_sort($array, $column, $order)
		{
			if (isset($array) && count($array))
			{
				foreach ($array as $key => $row)
				{
					$orderby[$key] = $row->$column;
				}

				if ($order)
				{
					array_multisort($orderby, SORT_DESC, $array);
				}
				else
				{
					array_multisort($orderby, SORT_ASC, $array);
				}
			}

			return $array;
		}
	}
}
