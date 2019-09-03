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

function array_trim($arr)
{
	return array_map('trim', $arr);
}

/*
This function is used by the jsdocs plugin
*/
function getUserInfontwrk($userId = null, $what = null)
{
	$db   = JFactory::getDBO();
	$data = array();

	// Return with empty data
	if ($userId == null || $userId == '')
	{
	}

	$user = JFactory::getUser($userId);

	if ($user->id == null)
	{
	}

	$data['id']    = $user->id;
	$data['name']  = $user->name;
	$data['email'] = $user->email;

	// Attach custom fields into the user object
	$strSQL = 'SELECT value.value '
	. 'FROM ' . $db->quoteName('#__community_fields') . ' AS field '
	. 'LEFT JOIN ' . $db->quoteName('#__community_fields_values') . ' AS value '
	. 'ON field.id=value.field_id AND value.user_id=' . $db->Quote($userId) . ' '
	. 'WHERE field.published=' . $db->Quote('1') . ' AND '
	. 'field.visible=' . $db->Quote('1') . ' AND '
	. 'field.id IN (' . $what . ') '
	. 'ORDER BY field.ordering';

	$db->setQuery($strSQL);
	$result = $db->loadColumn();

	if ($db->getErrorNum())
	{
		JError::raiseError(500, $db->stderr());
	}

	$result = array_filter($result);

	$s = "";
	$c = "";
	$c = implode(',', $result);
	$c = explode(',', $c);

	return $c;
}

/**
* This function is used by the jsntwrk suggest plugin
**/
function getUserInfo($userId = null,$what = null)
{
	$db   = JFactory::getDBO();
	$data = array();

	// Return with empty data
	if ($userId == null || $userId == '')
	{
	}

	$user = JFactory::getUser($userId);

	if ($user->id == null)
	{
	}

	$data['id']    = $user->id;
	$data['name']  = $user->name;
	$data['email'] = $user->email;

	// Attach custom fields into the user object
	$strSQL = 'SELECT value.value '
	. 'FROM ' . $db->quoteName('#__community_fields') . ' AS field '
	. 'LEFT JOIN ' . $db->quoteName('#__community_fields_values') . ' AS value '
	. 'ON field.id=value.field_id AND value.user_id=' . $db->Quote($userId) . ' '
	. 'WHERE field.published=' . $db->Quote('1') . ' AND '
	. 'field.visible=' . $db->Quote('1') . ' AND '
	. 'field.id IN (' . $what . ') '
	. 'ORDER BY field.ordering';
	$db->setQuery($strSQL);

	$result = $db->loadColumn();

	if ($db->getErrorNum())
	{
		JError::raiseError(500, $db->stderr());
	}

	$result = array_filter($result);

	$s = "";
	$c = "";
	$c = implode(',', $result);
	$c = explode(',', $c);
	$s .= implode('" "', $c);
	$s = ' "' . $s . '" ';

	return $s;
}
