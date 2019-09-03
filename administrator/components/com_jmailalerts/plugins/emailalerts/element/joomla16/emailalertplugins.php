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
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();


class JElementEmailalertplugins extends JElement {
	die('here');

  	var   $_name = 'Emailalertplugins';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		
		$db = &JFactory::getDBO();
		
		$db->setQuery("SELECT element AS value, name AS text 
		FROM #__plugins 
		WHERE folder = 'emailalerts' ");
		$options = $db->loadObjectList();
		
		return JHTML::_('select.genericlist', $options, $control_name.'['.$name.'][]', 'multiple="true" size="5"', 'value', 'text', $value);

	}
}
?>

