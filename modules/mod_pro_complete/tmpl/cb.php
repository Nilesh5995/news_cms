<?php
defined('_JEXEC') or die('Restricted access'); // no direct access

$document	=JFactory::getDocument();
$css		= Juri::base().'modules/mod_pro_complete/assets/mod_cbprocomplete.css';
$jss		= Juri::base().'modules/mod_pro_complete/assets/js/overlib_all_mini.js';
$document->addStyleSheet($css);
$document->addScript($jss);
global $Itemid;

$db = JFactory::getDBO();
$my = JFactory::getUser();

//Get Params
$param_fields=$params->get('cb_fields');
$count_field = count($param_fields);
$usersname = intval( $params->get( 'name', 1 ) );
$bar =  $params->get( 'bars', 'both' );
$show_tooltipfield=$params->get('tooltipfield',1);
$image = intval( $params->get( 'image', 1 ) );

// Login if registered user

if(!$my->id) {
	echo JText::_( 'MOD_PRO_COMPLETE_PRO_PLZ_LOGIN' );
	return;
}
//print_r ($param_fields);
$cnt=count($param_fields);
$tooltipfield=$pobj->tooltipfield;
//print_r($cnt);
$tootip='';

$arrs= implode(' <br/> ',  $pobj->eval);
$tootip= implode(' &#10; ',  $pobj->eval);

if($tooltipfield==1 && $tlp_whn_pro_complete==1)
{
	if(JVERSION>=3.0)
	{
		//start tooltip div
		if($show_tooltipfield)
		echo '<div data-toggle="tooltip" title="'.JText::_('MOD_PRO_COMPLETE_TOOLTIP') .''.$tootip.'" >';
	}
	else if($show_tooltipfield)
		echo '<div onmouseout="return nd();" onmouseover="return overlib(\''.$arrs.'\', CAPTION, \'&lt;label id=&quot;paramshelpsite-lbl&quot; for=&quot;paramshelpsite&quot; class=&quot;hasTip&quot; title=&quot;'.JText::_('MOD_PRO_COMPLETE').' :::&quot;&gt;'.JText::_('MOD_PRO_COMPLETE_FIELD_MESG').'&lt;/label&gt;\');">';

}
else if($tlp_whn_pro_complete==0)
{
	if($show_tooltipfield)
		echo '<div data-toggle="tooltip" title="'.JText::_('MOD_PRO_COMPLETE_MSG_PROFILE_COMPLETE').'" >';
	else
		echo '<div>';
	if(JVERSION>=3.0)
	{	if($show_tooltipfield)
			echo '<div style=""  id="container11" title="'.JText::_('MOD_PRO_COMPLETE').'" data-content="'.JText::_('MOD_PRO_COMPLETE_MSG_PROFILE_COMPLETE').''.$arrs.'" data-placement="top" data-toggle="popover" data-original-title="">';
		else
			echo '<div style="" >';
	}

}
$class_well='';
if($image==1)
{
	$class_well='well';
}
		if($tlp_whn_pro_complete==1)
		{	
			if(JVERSION>=3.0 AND $show_tooltipfield)
				echo '<div style="" class="'.$class_well.'" id="container11" title="'.JText::_('MOD_PRO_COMPLETE').'" data-content="'.JText::_('MOD_PRO_COMPLETE_FIELD_MESG').''.$arrs.'" data-placement="top" data-toggle="popover" data-original-title="">';
			else
				echo '<div style="" class="'.$class_well.'">';
		}
		else if($cb_user_image)
			echo '<div style="" class="'.$class_well.'" data-original-title="">';
		else
			echo '<div style="" class="" data-original-title="">';

						foreach($user_info as $user)
						{
							echo "<div style=\" margin: 0px; padding:0px; text-align:center\">";
							$link = JRoute::_('index.php?option=com_comprofiler&Itemid=' . $_Itemid);
							$link1 = JRoute::_('index.php?option=com_comprofiler&task=userDetails&Itemid=' . $_Itemid);
							if($usersname || $image)
							{
								echo '<div style="margin:2px; padding:5px; border:0px solid #CCC;overflow:hidden;">';
									$ueConfig="0";
									if($image == 1)
									{?>
										<a href="<?php echo $link; ?>">
											<img src="<?php echo $cb_user_image; ?>" border="0"  border="0" width="" height="" >
										</a>
										<?php
									}
									if($usersname==1)
									{
									?>
										<div>
											<a href="<?php echo $link; ?>"><?php echo $my->name;
											echo '</a>';
									echo '</div>';
									}
								echo '</div>';
							}
						 echo '</div>';
						}
			 if($tlp_whn_pro_complete==1 AND $show_tooltipfield)
			{	if(JVERSION>=3.0)
					echo '</div>';
			}
			else
				echo '</div>';

if($tooltipfield==1 && $tlp_whn_pro_complete==1)
{
	if($show_tooltipfield)
		echo '</div>';
}else if($tlp_whn_pro_complete==0)
{
	echo '</div>';
	if(JVERSION>=3.0)
		echo '</div>';
}

if( $bar =='both'|| $bar =='bar')
{
	if(JVERSION<3.0): ?>
	<div id='pro-comp-container'>
		<?php
	endif;
	if($tooltipfield==1)
	{
		if(JVERSION>=3.0)
		{	// start tooltip div
			if($tlp_whn_pro_complete==1)
			{
				echo '<div data-toggle="tooltip" title="'.JText::_('MOD_PRO_COMPLETE_TOOLTIP') .''.$tootip.'" >';
			}
			else 
			{
				echo '<div data-toggle="tooltip" title="'.JText::_('MOD_PRO_COMPLETE_MSG_PROFILE_COMPLETE').'" >';
			}
		}
		else if($tlp_whn_pro_complete==1)
			echo '<div onmouseout="return nd();" onmouseover="return overlib(\''.$arrs.'\', CAPTION, \'&lt;label id=&quot;paramshelpsite-lbl&quot; for=&quot;paramshelpsite&quot; class=&quot;hasTip&quot; title=&quot;'.JText::_('MOD_PRO_COMPLETE').' :::'.JText::_('MOD_PRO_COMPLETE_FIELD_MESG').'&quot;&gt;'.JText::_('MOD_PRO_COMPLETE_FIELD_MESG').'&lt;/label&gt;\');">';
		else
			echo '<div data-toggle="tooltip" title="'.JText::_('MOD_PRO_COMPLETE_MSG_PROFILE_COMPLETE').'" >';
	}
				if(JVERSION>=3.0): ?>
					<div class="progress progress-striped">
						<div class="bar bar-info" style="width: <?php echo $pobj->perc;?>%;">
						<?php if($bar == 'both'){ ?>
							<b style="color:#000000;"><?php echo $pobj->perc;?>%</b>
						<?php }?>
						</div>
					</div>
					<?php
				else: ?>
					<div id="box">
						<div id="bar" style="width:<?php echo $pobj->perc.'%'; ?>" >
							<?php if($bar == 'both') echo $pobj->perc.'%'; else echo ' <br/>'; ?>
						</div>
					</div>
					<?php
				 endif;?>

			<?php
	if($tooltipfield==1 AND (JVERSION>=3.0))
	{
		echo '</div>';
	}
	else if($tooltipfield==1)
	{
		echo '</div>';
	}

if(JVERSION<3.0)
	echo'</div>';
}
if(JVERSION<3.0): ?>
	<div id='pro-comp-container'>
		<?php
	endif;
if($bar == 'numeric' )
{
	//if(JVERSION<3.0):
		echo '<div class="alert alert-info">';
			echo JText::_( 'MOD_PRO_COMPLETE_PRO_PROFILE' ) ." ".$pobj->perc . "% ". JText::_( 'MOD_PRO_COMPLETE_PRO_PROFILES' );
				echo "<br/>";
		echo '</div>';
	//endif;
}
if(JVERSION>=3.0):
	?>
	<a href='<?php
		$msg=JText::_('MOD_PRO_COMPLETE_EDIT');
		echo $link1; ?>'>
		<div style="margin-left:25%;margin-top:1%;">
			<input id="LOGIN" class="btn btn-primary" type="button" value="<?php echo JText::_('MOD_PRO_COMPLETE_EDIT'); ?>">
		</div>
	</a>
	<?php
else :?>
	<a href="<?php echo $link1; ?>">[<?php echo JText::_('MOD_PRO_COMPLETE_EDIT'); ?>]</a>
	<?php
endif;

if(JVERSION<3.0)
echo '</div>';
?>
<script>
jQuery("#container11").popover({});
</script>
