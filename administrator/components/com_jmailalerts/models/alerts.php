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
class JmailalertsModelalerts extends JModelList
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
				'title', 'a.title',
				'description', 'a.description',
				'allow_users_select_plugins', 'a.allow_users_select_plugins',
				'respect_last_email_date', 'a.respect_last_email_date',
				'is_default', 'a.is_default',
				'allowed_freq', 'a.allowed_freq',
				'default_freq', 'a.default_freq',
				'email_subject', 'a.email_subject',
				'template', 'a.template',
				'template_css', 'a.template_css',
				'batch_size', 'a.batch_size',
				'enable_batch', 'a.enable_batch',
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


		//Filtering is_default
		$this->setState('filter.is_default', $app->getUserStateFromRequest($this->context.'.filter.is_default', 'filter_is_default', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_jmailalerts');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
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
		$query->from('`#__jma_alerts` AS a');


	// Join over the users for the checked out user.
	$query->select('uc.name AS editor');
	$query->select('freq.name AS frequencyname,freq.id freqid');
	$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
	$query->join('LEFT', '#__jma_frequencies AS freq ON freq.id=a.default_freq');


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
				$query->where(' a.title LIKE ' . $search);
			}
		}

		//Filtering is_default
		$filter_is_default = $this->state->get("filter.is_default");
		if ($filter_is_default != '') {
			$query->where("a.is_default = '".$db->escape($filter_is_default)."'");
		}


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}
		return $query;
	}
	function getPlugnames($tmplate)
	{
		$this->_db->setQuery('SELECT name, element,params FROM #__extensions WHERE folder=\'emailalerts\' AND enabled = 1');

		$plugcompair= $this->_db->loadObjectList();//return the plugin data array

		foreach($plugcompair as $plg)
		{
			if(strstr($tmplate,'['.$plg->element.']'))
			{
			 $plugname[]=$plg->element;
			}
		}
		if(isset($plugname[0])){
			$plugname = implode(', ',$plugname);
			return $plugname;
		}else{
			return JText::_('COM_JMAILALERTS_NO_PLUGINS_ENABLED_OR_INSTALLED');
		}
	}

	/**
	 * Method to set a alert as default.
	 *
	 * @return  boolean  True if successful.
	 * @throws	Exception
	 */
	public function setDefault($id = 0)
	{
		$user	= JFactory::getUser();
		$db		= $this->getDbo();
		$ids=implode(',',$id);
		$db->setQuery(
			'UPDATE #__jma_alerts' .
			' SET is_default = \'1\'' .
			' WHERE id IN( '.$ids.')'
		);
		$db->execute();
		// Clean the cache.
		$this->cleanCache();
		return true;
	}
	/**
	 * Method to unset a alert as default.
	 *
	 * @param   integer  The primary key ID for the style.
	 *
	 * @return  boolean  True if successful.
	 * @throws	Exception
	 */
	public function unsetDefault($id = 0)
	{
		$db= $this->getDbo();
		$ids=implode(',',$id);
		$db->setQuery(
			'UPDATE #__jma_alerts' .
			' SET is_default = \'0\'' .
			' WHERE id IN ('.$ids.')'
		);
		$db->execute();
		// Clean the cache.
		$this->cleanCache();
		return true;
	}
}
