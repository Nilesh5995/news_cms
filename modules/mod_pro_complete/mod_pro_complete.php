<?php

/**
* @package		Profile Completeness
* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link     http://www.techjoomla.com
**/
//don't allow other scripts to grab and execute our file
defined('_JEXEC') or die('Access denied.');
if(!defined('DS'))
{
	define('DS',DIRECTORY_SEPARATOR);
}
//include the helper file
require_once(dirname(__FILE__).DS.'helper.php');

$my=JFactory::getUser();
if(!$my->id)
{
	echo JText::_('MOD_PRO_COMPLETE_PRO_PLZ_LOGIN');
	return;
}
//check the component is installed
if($params->get('profiletype')=='js')
{
	if(!file_exists( JPATH_ROOT.DS.'components'.DS.'com_community') )
	{
		return;
	}
}
else
{
	if(!file_exists( JPATH_ROOT.DS.'components'.DS.'com_comprofiler') )
	{
		return;
	}
}
//get the items to display from the helper
$modProCompleteHelper=new modProCompleteHelper();
$pobj=$modProCompleteHelper->getItems($params);

if(!intval($params->get('complete'))){
	if(@$pobj->perc==100){
		return $pobj->perc;
	}
}

//To hide tooltip when profile 100% complete
$tlp_whn_pro_complete=1;
if(@$pobj->perc==100){
	$tlp_whn_pro_complete=0;
}
$_Itemid=$modProCompleteHelper->getItemId($params);

//call helper function get user info
$user_info=$modProCompleteHelper->getUserinfo($params);
//echo "<pre>";print_r($user_info);echo "</pre>";die;

//include the template for display
if($params->get('profiletype')=='js')
{
	//get the jomsocial user profile avtar
	$modProCompleteHelper=new modProCompleteHelper();
	$js_user_image='';
	if($user_info[0]->userid)
		$js_user_image=$modProCompleteHelper->getJomsocialUserAvatar($user_info[0]->userid);
	require(JModuleHelper::getLayoutPath('mod_pro_complete'));
}
else
{
	//get the cb user profile Avatar
	$modProCompleteHelper=new modProCompleteHelper();
	$cb_user_image='';
	if(isset($user_info[0]->user_id))
		$cb_user_image=$modProCompleteHelper->getCBUserAvatar($user_info[0]->user_id);
	require(JModuleHelper::getLayoutPath('mod_pro_complete','cb'));
}
?>
