<?php
/**
* @package	Profile Completeness
* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link http://www.techjoomla.com
**/
defined('_JEXEC') or die('Restricted access');

class modProCompleteHelper
{
	public function getItems($params)
	{
		global $Itemid;
		$db=JFactory::getDBO();
		$my=JFactory::getUser();
		$document=JFactory::getDocument();
		//Get Params
		if($params->get('profiletype')=='js')//js fields
			$param_fields=$params->get('js_fields');
		else//cb fields
			$param_fields=$params->get('cb_fields');

		$count_field=count($param_fields);
		$usersname=intval($params->get('name',1));
		$bar=$params->get('bars','both');

		$image=intval($params->get('image',1));

		if($params->get('profiletype')=='js')
			$img_path=JURI::base().'images/avatar';
		else
			$img_path=JURI::base().'images/comprofiler';
		$tooltipfield=intval($params->get('tooltipfield'));
		$arrp="";
		$earr=array();
		$flag=0;
		if($param_fields && is_array($param_fields))
		{
			foreach($param_fields as $paramfield)
			{
				if($params->get('profiletype')=='js')
				{
					$sql="SELECT v.value, f.name
					FROM `#__community_fields_values` as v
					INNER JOIN #__community_fields as f ON f.id=v.field_id
					WHERE v.user_id=".$my->id." AND v.field_id=".$paramfield;
					$db->setQuery($sql);
					$cnt=$db->loadAssoc('f.name');
					if($cnt['value']){
						$flag++;
					}
					else
					{
						if($cnt['name']){
							$earr[]	= $cnt['name'];
						}
						else
						{
							$sql="SELECT name
							FROM `#__community_fields`
							WHERE id=".$paramfield;
							$db->setQuery($sql);
							$filed_name=$db->loadResult();
							$earr[]=$filed_name;
						}
					}
				 }
				else//cb
				{
					$sql="SELECT ".$paramfield."
					FROM #__comprofiler, #__users
					WHERE user_id=".$my->id;
					$db->setQuery($sql);
					$cnt=$db->loadResult();
					if($cnt){
						$flag++;
					}
					else{
						$earr[]	= $paramfield;
					}
				}
			}
		}
		else if($param_fields)
		{
			if($params->get('profiletype')=='js')
			{
				$sql="SELECT v.value, f.name
				FROM `#__community_fields_values` as v
				INNER JOIN #__community_fields as f ON f.id=v.field_id
				WHERE v.user_id=".$my->id."
				AND v.field_id=".$param_fields;
				$db->setQuery($sql);
				$result=$db->loadAssoc();
				if($result['value'])
					$flag++;
				else
				{
					if($result['name'])
						$arrp=$result['name'];
					else
					{
						$sql="SELECT name
						FROM `#__community_fields`
						WHERE id=".$param_fields;
						$db->setQuery($sql);
						$filed_name=$db->loadResult();
						$arrp[]=$filed_name;
					}
				}
			}
			else
			{
				//echo $param_fields;
				$sql="SELECT ".$param_fields."
				FROM #__comprofiler
				WHERE user_id=".$my->id;
				$db->setQuery($sql);
				$cnt=$db->loadResult();
				if($cnt){
					$flag++;
				}
				else{
					$earr[]=$paramfield;
				}
			}
		}
		$obj=new stdClass();
		$obj->perc=0;
		$obj->eval=$earr;
		if($params->get('profiletype')=='js')
			$obj->eval1=$arrp;
		if($count_field)
			$obj->perc=round(($flag/$count_field)* 100);
		$obj->tooltipfield= $tooltipfield;
			return $obj;
	}

	function getItemId($params)
	{
		//Get Item id
		$db=JFactory::getDBO();
		if($params->get('profiletype' )=='cb')
		{
			$_Itemid="";
			$itemid="";
			$Itemid="";
			$db->setQuery("SELECT id FROM #__menu WHERE link LIKE '%com_comprofiler%'");
		}
		else//js
		{
			$db->setQuery("SELECT id FROM #__menu WHERE link LIKE '%com_community%'");
		}
		$itemid=$db->loadResult();
		return $_Itemid = $itemid ? $itemid : $Itemid;
	}

	function getUserinfo($params)
	{
		$id=JFactory::getUser()->id;
		//Get Item id
		$db=JFactory::getDBO();
		if($params->get( 'profiletype')=='js'){
			$query1="SELECT *
			FROM #__community_users
			WHERE userid=".$id;
		}
		else{
			$query1="SELECT *
			FROM #__comprofiler
			WHERE user_id=".$id."
			";
		}
		$db->setQuery($query1);
		return $users=$db->loadObjectList();
	}

	 /** Function CB user profile Avatar
	 *
	 * @param string $uimage to store image path
	 * @param string $jspath contain component path to check it exists
	 * @since   2.5
	 */
	function getCBUserAvatar($userid)
	{	
		$db=JFactory::getDBO();
		$q="SELECT a.id,a.username,a.name, b.avatar, b.avatarapproved 
			FROM #__users a, #__comprofiler b 
			WHERE a.id=b.user_id AND a.id=".$userid;
		$db->setQuery($q);
		$user=$db->loadObject();
		$img_path=JUri::root()."images/comprofiler";		
		if(isset($user->avatar) && isset($user->avatarapproved))
		{
			if(substr_count($user->avatar, "/") == 0)
			{
				$uimage = $img_path . '/tn' . $user->avatar;
			}
			else
			{
				$uimage = $img_path . '/' . $user->avatar;
			}
		}
		else if (isset($user->avatar))
		{//avatar not approved
			$uimage = JUri::root()."/components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png";
		}
		else
		{//no avatar
			$uimage = JUri::root()."/components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png";
		}		
		return $uimage;
	}

	 /** Function Jomsocial user profile Avatar
	 *
	 * @param string $uimage to store image path
	 * @param string $jspath contain component path to check it exists
	 * @since   2.5
	 */
	function getJomsocialUserAvatar($userid)
	{
		$mainframe=JFactory::getApplication();
		/*included to get jomsocial avatar*/
		$uimage='';
		$jspath=JPATH_ROOT.DS.'components'.DS.'com_community';
		if(file_exists($jspath)){
			include_once($jspath.DS.'libraries'.DS.'core.php');
			$user=CFactory::getUser($userid);
			$uimage=$user->getThumbAvatar();        
			if(!$mainframe->isSite())
			{
				$uimage=str_replace('administrator/','',$uimage);
			}
		}
		return $uimage;
	}
}
?>
