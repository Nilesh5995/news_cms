<?php 

	// Check to ensure this file is within the rest of the framework
	defined('JPATH_BASE') or die();
	jimport('joomla.html.html');
	jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
	class JFormFieldJsfields extends JFormFieldList
	{
		var $_name = 'Jsfields';
		protected function getOptions() 
		{
			if( file_exists( JPATH_ROOT.'/components/com_community') )
			{		
				// Get the database object and a new query object. 
				$db = JFactory::getDBO(); 
				$query = $db->getQuery(true); 
				$allowed_types = "'text','textarea','country','url','select','birthdate'";
				//Build the query. 
				$query="SELECT id AS value, name AS text 
						FROM #__community_fields 
						WHERE published = 1 AND type IN ($allowed_types)
						";
				// Set the query and load the options. 
				$db->setQuery($query); 
				$options = $db->loadObjectList(); 
				// Check for a database error. 
				if ($db->getErrorNum())
				{ 
					JError::raiseWarning(500, $db->getErrorMsg()); 
				}  
			
				if($options)
				{
					foreach ($options as $i=>$option) 
					{ 
						$options[$i]->text = JText::_($option->text); 
					}
					//Merge any additional options in the XML definition. 
					$options = array_merge(parent::getOptions(), $options);
					return $options; 
				} 
				else
				{
					return JText::_('MOD_PRO_COMPLETE_NO_FIELDS_FOUND');
				}
			}
			else
			{
				return JText::_('MOD_PRO_COMPLETE_NO_FIELDS_FOUND');
			}			
		}
	}
?>
