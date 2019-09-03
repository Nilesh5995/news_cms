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
 * subscriber Table class
 */
class JmailalertsTablesubscriber extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__jma_subscribers', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param	array		Named array
     * @return	null|string	null is operation was satisfactory, otherwise returns an error
     * @see		JTable:bind
     * @since	1.5
     */
    public function bind($array, $ignore = '') {


		$input = JFactory::getApplication()->input;

		$task = $input->getString('task', '');

		if(($task == 'save' || $task == 'apply') && (!JFactory::getUser()->authorise('core.edit.state','com_jmailalerts') && $array['state'] == 1)){
			$array['state'] = 0;

		}//@Amol Make validation that User should not subcribe one alert more than one time (Means no duplicate entries of alert against user)
		else if(($task == 'save' || $task == 'apply' || $task=='save2new' || $task=='save2copy'))
		{
			$db=JFactory::getDBO();
			$query=$db->getQuery(true);
			$query->SELECT('id,alert_id');
			$query->from('`#__jma_subscribers` AS subs');

			//if is not guest then check alert subcribed against user id
			if(!$array['user_id']==0)
			{
				$query->where('alert_id='.$array['alert_id'].' AND user_id='.$array['user_id']);
			}
			else //if is guest then check alert subcribed against user email id
			{
				$query->where("alert_id=".$array['alert_id']." AND email_id='".$array['email_id']."'");
			}

			$db->setQuery($query);
			$result=$db->loadColumn();
			if($array['id'])// check for update the user alert, if alert present against the user id then skip the count 1 (means updating the alert) otherwise avoid to add same alert agains the user
			{
				if(count($result)>1)
				{
					$this->setError(JText::_('COM_JMAILALERTS_DUPLICATE_ALERT_ERROR'));
					return false ;
				}
			}
			else if(count($result)>=1) //check for adding the new alert against the user , if alert present against user id then it will give atleast one count of ids & avoid to add same alert agains the user
			{
				$this->setError(JText::_('COM_JMAILALERTS_DUPLICATE_ALERT_ERROR'));
				return false;
			}
			if($array['user_id']!=0) //if subscription is new or user details not present then get user name & email id $array['name']=user name
			{
				$query=$db->getQuery(true);
				$query->SELECT('id,name,email');
				$query->from('`#__users`');
				$query->where('id='.$array['user_id']);
				$db->setQuery($query);
				$user_data=$db->loadObject();
				if($user_data)
				{
					$array['name']=$user_data->name; //user name
					$array['email_id']=$user_data->email; //user email
				}
				else //This 'user id' is not present in our database, Please check the 'user id' !
				{
					$this->setError(JText::_('COM_JMAILALERTS_USER_NOT_PRESENT_ERROR'));
					return false;
				}
			}

			if(!$array['id']) //adding the new alert against the user then add plugins for the user
			{
				$ManageUserHelper=new ManageUserHelper();
				$user_data=$ManageUserHelper->SubscribeUser($array);
				$array['plugins_subscribed_to']=$user_data['plugins_subscribed_to'];
				$array['date']=$user_data['date'];
			}
		}

        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }
        if(!JFactory::getUser()->authorise('core.admin', 'com_jmailalerts.subscriber.'.$array['id'])){
            $actions = JFactory::getACL()->getActions('com_jmailalerts','subscriber');
            $default_actions = JFactory::getACL()->getAssetRules('com_jmailalerts.subscriber.'.$array['id'])->getData();
            $array_jaccess = array();
            foreach($actions as $action){
                $array_jaccess[$action->name] = $default_actions[$action->name];
            }
            $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
        }
        //Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules'])) {
			$this->setRules($array['rules']);
		}

        return parent::bind($array, $ignore);
    }

    /**
     * This function convert an array of JAccessRule objects into an rules array.
     * @param type $jaccessrules an arrao of JAccessRule objects.
     */
    private function JAccessRulestoArray($jaccessrules){
        $rules = array();
        foreach($jaccessrules as $action => $jaccess){
            $actions = array();
            foreach($jaccess->getData() as $group => $allow){
                $actions[$group] = ((bool)$allow);
            }
            $rules[$action] = $actions;
        }
        return $rules;
    }

    /**
     * Overloaded check function
     */
    public function check() {

        //If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0) {
            $this->ordering = self::getNextOrder();
        }

        return parent::check();
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param    mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param    integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    integer The user id of the user performing the operation.
     * @return    boolean    True on success.
     * @since    1.0.4
     */
    public function publish($pks = null, $state = 1, $userId = 0) {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        } else {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
                'UPDATE `' . $this->_tbl . '`' .
                ' SET `state` = ' . (int) $state .
                ' WHERE (' . $where . ')' .
                $checkin
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin each row.
            foreach ($pks as $pk) {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->state = $state;
        }

        $this->setError('');
        return true;
    }

    /**
      * Define a namespaced asset name for inclusion in the #__assets table
      * @return string The asset name
      *
      * @see JTable::_getAssetName
    */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_jmailalerts.subscriber.' . (int) $this->$k;
    }

    /**
      * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
      *
      * @see JTable::_getAssetParentId
    */
    //protected function _getAssetParentId($table = null, $id = null){
	protected function _getAssetParentId(JTable $table = NULL, $id = NULL){
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_jmailalerts');
        // Return the found asset-parent-id
        if ($assetParent->id){
            $assetParentId=$assetParent->id;
        }
        return $assetParentId;
    }
}
