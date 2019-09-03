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
	jimport('joomla.html.html');
	class JFormFieldJsfieldsgender extends JFormFieldList
	{
		var $_name = 'Jsfieldsgender';
		protected function getOptions() 
		{ 
			// Get the database object and a new query object. 
			$db = JFactory::getDBO(); 
			$query = $db->getQuery(true); 
			$type = "'select','checkbox','multicheckbox','radio'";
			
			$db->setQuery("SELECT id AS value, name AS text 
			FROM #__community_fields 
			WHERE published = 1 AND type IN ({$type})");
			
			$options = $db->loadObjectList(); 
		
			// Check for a database error. 
			if ($db->getErrorNum()){ 
				JError::raiseWarning(500, $db->getErrorMsg()); 
			}  
		
			if($options)
			{
				foreach ($options as $i=>$option) { 
					$options[$i]->text = JText::_($option->text); 
				}
				//Merge any additional options in the XML definition. 
				$options = array_merge(parent::getOptions(), $options);
				return $options; 
			} 
			else{
				return JText::_('NO_GENDER_FIELDS_FOUND');
			}
		} 
	}
?>
