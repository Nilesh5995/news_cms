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
 
/**
 * Renders a multiple item select element 
 * using SQL result and explicitly specified params
 *
 */
 
class JElementSQLMultiListX extends JElement
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        var    $_name = 'SQLMultiListX';
 
        function fetchElement($name, $value, &$node, $control_name)
        {
             //  print_r($value);die;
                // Base name of the HTML control.
                $ctrl  = $control_name .'['. $name .']';
 
                // Construct the various argument calls that are supported.
                $attribs       = ' ';
                if ($v = $node->attributes( 'size' )) {
                        $attribs       .= 'size="'.$v.'"';
                }
                if ($v = $node->attributes( 'class' )) {
                        $attribs       .= 'class="'.$v.'"';
                } else {
                        $attribs       .= 'class="inputbox"';
                }
                if ($m = $node->attributes( 'multiple' ))
                {
                        $attribs       .= ' multiple="multiple"';
                        $ctrl          .= '[]';
                }
 
                // Query items for list.
                                $db                    = & JFactory::getDBO();
                                $db->setQuery($node->attributes('sql'));
                                $key = ($node->attributes('key_field') ? $node->attributes('key_field') : 'value');
                                $val = ($node->attributes('value_field') ? $node->attributes('value_field') : $name);
 
                $options = array ();
                foreach ($node->children() as $option)
                {
                        $options[]= array($key=> $option->attributes('value'),$val => $option->data());
                }
 
                $rows = $db->loadAssocList();
                if($rows){
                	foreach ($rows as $row){
                        $options[]=array($key=>$row[$key],$val=>$row[$val]);
                	}
                }
                
                if($options){
                        return JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name.$name);
                }else{
                        return JText::_("No data to display here");
                }
        }
}

