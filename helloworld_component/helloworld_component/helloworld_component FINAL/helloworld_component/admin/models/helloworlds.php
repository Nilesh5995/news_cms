<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorldList Model
 *
 * @since  0.0.1$row
 */
class HelloWorldModelHelloWorlds extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'greeting',
				'published',
				'email',
				'mobile',
				'author',
				'created',
				'ordering',
				'category_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('a.id as id, a.greeting as greeting, a.published as published, a.email as email, a.mobile as mobile,  a.created as created, a.image as imageInfo, a.latitude as latitude, a.longitude as longitude, a.checked_out as checked_out, a.checked_out_time as checked_out_time, a.ordering as ordering, a.alias as alias')
		->from($db->quoteName('#__helloworld', 'a'));
		// Join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid');
		// Join with users table to get the username of the author
		$query->select($db->quoteName('u.username', 'author'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = a.created_by');
		// Join with users table to get the username of the person who checked the record out
		$query->select($db->quoteName('u2.username', 'editor'))
			->join('LEFT', $db->quoteName('#__users', 'u2') . ' ON u2.id = a.checked_out');
		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('greeting LIKE ' . $like);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}
		// Filter by categories
		$catid = $this->getState('filter.category_id');
		if ($catid)
		{
			$query->where("a.catid = " . $db->quote($db->escape($catid)));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'greeting');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;


	}
}