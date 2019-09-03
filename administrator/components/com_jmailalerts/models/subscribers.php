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

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Jmailalerts records.
 */
class JmailalertsModelsubscribers extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 * @since    1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
								'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'user_id', 'a.user_id',
				'alert_id', 'a.alert_id',
				'name', 'a.name',
				'email_id', 'a.email_id',
				'frequency', 'a.frequency',
				'date', 'a.date',
				'plugins_subscribed_to', 'a.plugins_subscribed_to',

			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);


		//Filtering alert_id
		$this->setState('filter.alert_id', $app->getUserStateFromRequest($this->context.'.filter.alert_id', 'filter_alert_id', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_jmailalerts');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.user_id', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__jma_subscribers` AS a');
		//get the frequency name by frequency id
		$query->select('freq.name AS frequencyname,freq.id freqid');
		$query->join('LEFT', '`#__jma_frequencies` AS freq ON freq.id=a.frequency');

		//get the alert name by alert id
		$query->select('alert.title AS alert_name');
		$query->join('LEFT', '`#__jma_alerts` AS alert ON alert.id=a.alert_id');

	// Filter by published state
	$published = $this->getState('filter.state');
	if (is_numeric($published)) {
		$query->where('a.state = '.(int) $published);
	} else if ($published === '') {
		$query->where('(a.state IN (0, 1))');
	}


		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('( a.user_id LIKE '.$search.'  OR  a.name LIKE '.$search.' )');
			}
		}



		//Filtering alert_id
		$filter_alert_id = $this->state->get("filter.alert_id");
		if ($filter_alert_id) {
			$query->where("a.alert_id = '".$db->escape($filter_alert_id)."'");
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}

		return $query;
	}

	public function getFilterOptionsAlert()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.id, a.title')
			->from('`#__jma_alerts` AS a')
			->where('a.state=1');
		$db->setQuery($query);

		return $db->loadobjectList();
	}
}
