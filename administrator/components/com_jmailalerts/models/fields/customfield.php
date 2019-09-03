<?php
/**
 * @version    SVN: <svn_id>
 * @package    JMailAlerts
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldCustomfield extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'text';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		switch($this->name)
		{
			case 'jform[allowed_freq][]':
				return $this->fetchFrequencies($this->name,$this->value,$this->element,$this->options['control'], 'allowed_freq');
			break;

			case 'jform[default_freq]':
				return $this->fetchDefaultFrequencies($this->name,$this->value,$this->element,$this->options['control'], 'default_freq');
			break;

			case 'jform[alert_id]':
				return $this->fetchAlerts($this->name,$this->value,$this->element,$this->options['control'], 'alert_id');
			break;

			case 'jform[frequency]':
				return $this->fetchDefaultFrequencies($this->name,$this->value,$this->element,$this->options['control'], 'frequency');
			break;
		}

	}
	/**
	 * Method to genereate list of allowed frequencies
	 * @return	list	The list of frequencies
	 */
	function fetchFrequencies($name, $value, &$node, $control_name, $id)
	{
		$db=JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('freq.id,freq.name FROM `#__jma_frequencies` as freq');
		$query->where('freq.state=1');
		$db->setQuery($query);
		$frequencies=$db->loadObjectList();
		$options = array();
		foreach($frequencies as $frequency){
			$options[] = JHtml::_('select.option',$frequency->id, JText::_($frequency->name));
		}
		return JHtml::_('select.genericlist',  $options, $name, 'class="inputbox required"  multiple="multiple" size="5"  ', 'value', 'text', $value, 'jform_' . $id);
	}
	/**
	 * Method to genereate list of allowed frequencies
	 * @return	list	The list of frequencies
	 */
	function fetchDefaultFrequencies($name, $value, &$node, $control_name, $id)
	{
		$db=JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('freq.id,freq.name FROM `#__jma_frequencies` as freq');
		$query->where('freq.state=1');
		$db->setQuery($query);
		$frequencies=$db->loadObjectList();
		$options = array();
		foreach($frequencies as $frequency){
			$options[] = JHtml::_('select.option',$frequency->id, JText::_($frequency->name));
		}
		return JHtml::_('select.genericlist',  $options, $name, 'class="inputbox required"', 'value', 'text', $value, 'jform_' . $id);
		//return JHtml::_('select.genericlist', $options, $fieldName, 'class="inputbox required"', 'value', 'text', $value, $control_name.$name );
	}
	/**
	 * Method to get the list of alerts
	 * @return	list	The list of alerts
	 */
	function fetchAlerts($name, $value, &$node, $control_name, $id)
	{
			$db=JFactory::getDbo();
			$query= $db->getQuery(true);
			$query->select('id,title FROM `#__jma_alerts`');
			$query->where('state=1');
			$db->setQuery($query);
			$alertnames= $db->loadObjectList();
			$options=array();
			foreach($alertnames as $alertname)
			{
				$options[]= JHtml::_('select.option', $alertname->id,$alertname->title);
			}
			return JHtml::_('select.genericlist',  $options, $name, 'class="inputbox required"', 'value', 'text', $value, 'jform_' . $id);
	}
}
