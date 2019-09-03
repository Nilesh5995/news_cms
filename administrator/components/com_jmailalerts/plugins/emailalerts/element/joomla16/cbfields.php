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

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 * @since  2.5.1
 */
class JFormFieldCbfields extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 * @since 1.6
	 */
	protected $type = 'Cbfields';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array  An array of JHtml options.
	 *
	 * @since   2.5.1
	 */
	protected function getOptions()
	{
		jimport('joomla.filesystem.folder');

		$cbFolder = JPATH_ADMINISTRATOR . '/components/com_comprofiler';

		if (!JFolder::exists($cbFolder))
		{
			return array();
		}

		// Get the database object and a new query object.
		$db      = JFactory::getDBO();
		$query   = $db->getQuery(true);

		// Build the query.
		$type = "'text','textarea','select','multiselect','checkbox','multicheckbox','radio'";

		$query = "SELECT name AS value, title AS text
		 FROM #__comprofiler_fields
		 WHERE `table` LIKE '#__comprofiler'
		 AND published = 1
		 AND type IN (" . $type . ")";

		// Set the query and load the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			// JError::raiseWarning(500, $db->getErrorMsg());

			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		if ($options)
		{
			foreach ($options as $i => $option)
			{
				$options[$i]->text = JText::_($option->text);
			}

			// Merge any additional options in the XML definition.
			$options = array_merge(parent::getOptions(), $options);

			return $options;
		}
		else
		{
			return JText::_('NO_FIELDS_FOUND');
		}
	}
}
