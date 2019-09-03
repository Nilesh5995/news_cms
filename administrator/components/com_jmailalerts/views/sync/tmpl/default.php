<?php
/**
 * @package     JMailAlerts
 * @subpackage  com_jmailalerts
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.html.pane');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>

<script type="text/javascript">
	techjoomla.jQuery(document).ready(function()
	{
		techjoomla.jQuery('.advanceoption').hide();
		loadFrequencies('alert_list');
		techjoomla.jQuery('#batch_size').val(400);
		techjoomla.jQuery('.showhide_progressbar').hide();
	});

	/* Convert the php date format to standard javascript date format*/
	function parseDate(input, format)
	{
		/*default format*/
		format = format || 'yyyy-mm-dd';
		var parts = input.match(/(\d+)/g),
		i = 0, fmt = {};
		/*extract date-part indexes from the format*/
		format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });

		return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]);
	}

	function generateFreq(data)
	{
		var rr=new Date();;
		var daterangefrom=techjoomla.jQuery('#last_email_date').val();
		var select=techjoomla.jQuery('#freq_id');
		select.find('option').remove().end();
		options=data.options;
		for(index = 0; index < data.length; ++index)
		{
		 	var freq=data[index];
			var op="<option value="  +freq['alertid']+  ">"  +freq['name']+   '</option>'     ;
			techjoomla.jQuery('#freq_id').append(op);
			techjoomla.jQuery("select").trigger("liszt:updated");
			techjoomla.jQuery('#last_email_date').val(freq['last_email_date']);
		}
		techjoomla.jQuery("#freq_id").trigger("chosen:updated");
		techjoomla.jQuery('#freq_id').trigger('liszt:updated');
	}

	function advanceoption_hideshow()
	{
		if(document.getElementById("advaced_options").checked===true)
		{
			techjoomla.jQuery('.advanceoption').show();
		}
		else
		{
			techjoomla.jQuery('.advanceoption').hide();
		}
		/*hide option readd usub user on document ready call show hide function*/
		ShowHideReaddUnsubsUser();
	}

	function loadFrequencies(alertid)
	{
		var id=techjoomla.jQuery('#'+alertid).val();
		/*call ajax function to get list of frequencies*/
		techjoomla.jQuery.ajax({
			url:'<?php echo jUri::base();?>'+'index.php?option=com_jmailalerts&task=loadFrequencies&alertid='+id+'&tmpl=component&format=row',
			type:'GET',
			async: false,
			dataType:'json',
			success:function(data){
				if (data === undefined || data === null || data.length <=0)
				{
					var op='<option value="">'+"<?php echo JText::_('COM_JMAILALERTS_FREQUENCIES');?>"+'</option>';
					select=techjoomla.jQuery('#freq_id');
					select.find('option').remove().end();
					select.append(op);
				}
				else
				{
					generateFreq(data);
				}
			}
		});
		techjoomla.jQuery("#freq_id").trigger("chosen:updated");
		techjoomla.jQuery('#freq_id').trigger('liszt:updated');
		/*call function to load subscription report*/
		LoadSubscriptionReport(id);
	}

	/*
	load the Subscription report
	*/
	/*global variable for data*/
	var data1,id1;

	function LoadSubscriptionReport(id)
	{
		if (!id){
			return false;
		}

		techjoomla.jQuery.ajax({
			url:'<?php echo jUri::base();?>'+'index.php?option=com_jmailalerts&task=getSubscribesCount&alertid='+id+'&tmpl=component&format=row',
			type:'GET',
			async: false,
			dataType:'json',
			success:function(data){
				data1=data;
				id1=id;
				getSubscriptionReport(data,id);
			}
		});
	}

	/*Method to generate subscription Report the Subscription report*/
	function getSubscriptionReport(data,id)
	{
		/*Before Sync*/
		techjoomla.jQuery('.subs_registerd').html(data[id]['registed_users']);
		techjoomla.jQuery('.subs_guest').html(data[id]['guest_users']);
		techjoomla.jQuery('.unsub_registerd').html(data[id]['unsubscribed_users']);
		techjoomla.jQuery('.unsubs_guest').html(data[id]['unsub_guest_users']);
		techjoomla.jQuery('.never_opted_in').html(data[id]['not_opted_user']);

		/*After Sync*/
		var after_sync_subs_registerd,overwrite_user_pref,after_sync_guest,after_sync_unsub_registerd,after_sync_unsub_guest;

		/*Registerd count*/
		after_sync_subs_registerd=parseInt(data[id]['registed_users'])+parseInt(data[id]['not_opted_user']);
		after_sync_guest=parseInt(data[id]['guest_users']);

		/*unsub count*/
		after_sync_unsub_registerd=parseInt(data[id]['unsubscribed_users']);
		after_sync_unsub_guest=parseInt(data[id]['unsub_guest_users']);

		if(document.adminForm.advaced_options.checked===true)
		{
			overwrite_user_pref=techjoomla.jQuery('input:radio[name="user_pref"]:checked').val();
			/*if overwrite user pref 'yes' then check the option Re-add unsubscribed user again option value 'Yes/No'*/
			if(overwrite_user_pref!=0)
			{
				var readd_unsub_user=techjoomla.jQuery('input:radio[name="unsub_user"]:checked').val();
				if(readd_unsub_user!=0)
				{
					after_sync_subs_registerd=parseInt(data[id]['registed_users'])+parseInt(data[id]['not_opted_user'])+parseInt(data[id]['unsubscribed_users']);
					after_sync_guest=parseInt(data[id]['guest_users'])+parseInt(data[id]['unsub_guest_users']);
					after_sync_unsub_registerd=0;
					after_sync_unsub_guest=0;
				}
			}
		}
		else
		{
			/*after normal sync only means no other options*/
		}

		/*after sync registerd user count*/
		techjoomla.jQuery('.after_sync_subs_registerd').html(after_sync_subs_registerd);
		//after sync guest user count
		techjoomla.jQuery('.after_sync_guest').html(after_sync_guest);
		//after sync unsub register count
		techjoomla.jQuery('.after_unsub_registerd').html(after_sync_unsub_registerd);
		//after sync unsub guest count
		techjoomla.jQuery('.after_unsub_guest').html(after_sync_unsub_guest);

		//calculate TOTAL BEFORE SYNC
		var column1_total=parseInt(data[id]['registed_users'])+parseInt(data[id]['unsubscribed_users'])+parseInt(data[id]['not_opted_user']);
		techjoomla.jQuery('.column1_total').html(column1_total);

		var column2_total=parseInt(data[id]['guest_users'])+parseInt(data[id]['unsub_guest_users']);
		techjoomla.jQuery('.column2_total').html(column2_total);

		//calculate TOTAL AFTER SYNC
		var column3_total=parseInt(after_sync_subs_registerd)+parseInt(after_sync_unsub_registerd);
		techjoomla.jQuery('.column3_total').html(column3_total);

		var column4_total=parseInt(after_sync_guest)+parseInt(after_sync_unsub_guest);
		techjoomla.jQuery('.column4_total').html(column4_total);

	}

	var percent=0;
	/**
	set_firs_ajax_call => if it is zero then , this is the first ajax request to get the total number of user to sync
	*/
	function sync(batch_size,set_firs_ajax_call,completed_batch_users)
	{
		// Get the selected alert id
		let alertid = document.getElementById('alert_list').value;

		if (!alertid) {
			return false;
		}

		techjoomla.jQuery('.showhide_progressbar').show();
		techjoomla.jQuery('.bar').css('width',0+'%');
		techjoomla.jQuery('.completed_percent').html(0+'%');
		setTimeout(sync2(batch_size,set_firs_ajax_call,completed_batch_users), 10000);
	}
	function sync2(batch_size,set_firs_ajax_call,completed_batch_users)
	{
		var xmlhttp;
		var alertid,last_email_date,default_frequency=0;
		var advanced_options_checked=0,overwrite_user_pref=0;
		var readd_unsub_user=0;

		if (window.XMLHttpRequest) {
		  // code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		}
		else{
		  // code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4){
				var server_data = xmlhttp.responseText;
				if(server_data == "No Users"){
					percent =100;
					techjoomla.jQuery('.bar').css('width',Math.round(percent)+'%');
					techjoomla.jQuery('.completed_percent').html(Math.round(percent)+'%');
					LoadSubscriptionReport(alertid);
					alert("Done.");
					return;
				}
				else if(server_data == "Insertion error"){
					alert("Some error occured while inserting data into the jos_email_alert table. Retry.");
					return;
				}
				else
				{
					if(set_firs_ajax_call==0)
					{
						total_users=server_data;
					}
					set_firs_ajax_call=set_firs_ajax_call+1;

					//calulate the sync completeness percentage
					completed_batch_users = parseInt(completed_batch_users) + parseInt(batch_size);
					if(parseInt(completed_batch_users)>=parseInt(total_users))
					{
						percent =100;
						techjoomla.jQuery('.bar').css('width',Math.round(percent)+'%');
						techjoomla.jQuery('.completed_percent').html(Math.round(percent)+'%');

					}
					else
					{
						percent = (parseInt(completed_batch_users) / parseInt(total_users)) * 100;
						techjoomla.jQuery('.bar').css('width',Math.round(percent)+'%');
						techjoomla.jQuery('.completed_percent').html(Math.round(percent)+'%');
					}
					//call recursively sync function for batch size
					//E.g total number of user is 20 & batch size is 5 then 4 times sync is call means 4 timens ajax request
					//send to the server
					sync2(batch_size,set_firs_ajax_call,completed_batch_users);
				}
			}
		}

		//get the selected alert id
		alertid=document.getElementById('alert_list').value;

		if(document.adminForm.advaced_options.checked===true)
		{
			advanced_options_checked=1;
			last_email_date=document.getElementById('last_email_date').value //get the last email date being synced
			default_frequency=document.getElementById('freq_id').value //get the default frequency id
			batch_size=document.getElementById('batch_size').value;
			overwrite_user_pref=techjoomla.jQuery('input:radio[name="user_pref"]:checked').val();

			// if overwrite user pref 'yes' then check the option Re-add unsubscribed user again option value 'Yes/No'
 			if(overwrite_user_pref!=0)
			{
				readd_unsub_user=techjoomla.jQuery('input:radio[name="unsub_user"]:checked').val();
			}
		}
		xmlhttp.open("GET","index.php?option=com_jmailalerts&view=ajaxsync&format=raw&set_firs_ajax_call="+set_firs_ajax_call+"&alertid="+alertid+"&advanced_options_checked="+advanced_options_checked+"&default_frequency="+default_frequency+"&last_email_date="+last_email_date+"&batch_size="+batch_size+"&overwrite_user_pref="+overwrite_user_pref+"&readd_unsub_user="+readd_unsub_user,true);
		xmlhttp.send(null);
	}
	function ShowHideReaddUnsubsUser()
	{
		var status;
		status=techjoomla.jQuery('input:radio[name="user_pref"]:checked').val();
		if(status==1)
		{
			techjoomla.jQuery('.ShowHideReaddUnsubsUserCls').show();
		}
		else
		{
			techjoomla.jQuery('.ShowHideReaddUnsubsUserCls').hide();
		}
		getSubscriptionReport(data1,id1);
	}
	/**
	method to change the subscription report value on click of radio options
	*/
	function chaneSubsreport()
	{
		getSubscriptionReport(data1,id1);
	}
	/**
	On click on joomla toolbar button cancel it will redirect to the cp view of jmailalerts
	*/
	Joomla.submitbutton = function(task)
	{
		if(task == 'adminForm.cancel'){
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else{
			if (task != 'adminForm.cancel' && document.formvalidator.isValid(document.id('adminForm'))) {
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}

</script>

<div class="<?php echo JMAILALERTS_WRAPPER_CLASS;?>" id="jmailalerts-sync">
	<form action="index.php?option=com_jmailalerts"  method="POST" name="adminForm" ENCTYPE="multipart/form-data" id="adminForm" class="form-horizontal">
	<?php if(!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
		<?php
		if(!empty($this->plugin_data))
		{
			//if there are plugins found in the `plugins` table, only then add the HTMl controls; else, display message
		}
		?>
		<div class="row-fluid">
			<div class="span8">
				<div class="control-group">
					<label class="control-label" for="alert_list" title="<?php echo JText::_('COM_JMAILALERTS_ALERT_TITLE_TOOLTIP');?>">
						<?php echo JText::_('COM_JMAILALERTS_ALERT_TITLE');?>
					</label>
					<div class="controls">
						<?php echo $this->dropdown=JHtml::_('select.genericlist',$this->alertname,'alert_name','required="required" aria-invalid="false" size="1" onchange="loadFrequencies(id)"','value','text','','alert_list'); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="advaced_options" title="<?php echo JText::_('COM_JMAILALERTS_ADVANCE_OPTION_TOOLTIP');?>">
						<?php echo JText::_('COM_JMAILALERTS_ADVANCE_OPTION');?>
					</label>
					<div class="controls">
						<input type="checkbox" name="advaced_options" id="advaced_options" onchange="advanceoption_hideshow()"/>
					</div>
				</div>
				<div class="control-group advanceoption">
					<label class="control-label" for="freq_id" title="<?php echo JText::_('COM_JMAILALERTS_FREQ_TOOLTIP');?>">
						<?php echo JText::_('COM_JMAILALERTS_FREQ');?>
					</label>
					<div class="controls">
						<select disabled name="freq_name" id="freq_id"></select>
					</div>
				</div>
				<div class="control-group advanceoption">
					<label class="control-label" for="last_email_date" title="<?php echo JText::_('COM_JMAILALERTS_LAST_EMAIL_DATE_TOOLTIP');?>">
						<?php echo JText::_('COM_JMAILALERTS_LAST_EMAIL_DATE');?>
					</label>
					<div class="controls">
						<?php
							$date = JFactory::getDate()->Format(JText::_('COM_JMAILALERTS_DATE_FORMAT_PHP'));

							// Set date to current date
							echo $calendar = JHtml::_('calendar', $date, 'last_email_date', 'last_email_date', JText::_('COM_JMAILALERTS_DATE_FORMAT_JOOMLA'), 'class="input input-medium"');?>
					</div>
				</div>
				<div class="control-group advanceoption">
					<label class="control-label" for="batch_size" title="<?php echo JText::_('COM_JMAILALERTS_BATCH_SIZE_TOOLTIP');?>">
						<?php echo JText::_('COM_JMAILALERTS_BATCH_SIZE');?>
					</label>
					<div class="controls">
						<input type="text" name="batch_size" id="batch_size" class="valid-numeric input input-medium" />
					</div>
				</div>
				<div class="control-group advanceoption">
					<label class="control-label" for="user_pref" title="<?php echo JText::_('COM_JMAILALERTS_OVERWRITE_USER_PREF_TOOLTIP');?>">
						<?php echo JText::_('COM_JMAILALERTS_OVERWRITE_USER_PREF');?>
					</label>
					<div class="controls ">
						<label class="radio inline">
							<input type="radio" class="btn-group" name="user_pref" id="user_pref1" value="1" onclick="ShowHideReaddUnsubsUser()"/><?php echo JText::_('COM_JMAILALERTS_YES');?>
						</label>
						<label class="radio inline">
							<input type="radio" class="btn-group" name="user_pref" id="user_pref2" value="0" checked="checked" onclick="ShowHideReaddUnsubsUser()"/><?php echo JText::_('COM_JMAILALERTS_NO');?>
						</label>
					</div>
				</div>
				<div class="control-group advanceoption ShowHideReaddUnsubsUserCls">
					<label class="control-label" for="unsub_user" title="<?php echo JText::_('COM_JMAILALERTS_ADD_UNSUB_USERS_TOOLTIP');?>">
						<?php echo JText::_('COM_JMAILALERTS_ADD_UNSUB_USERS');?>
					</label>
					<div class="controls">
						<label class="radio inline">
							<input type="radio" name="unsub_user" id="unsub_user1" value="1" onclick="chaneSubsreport()"/><?php echo JText::_('COM_JMAILALERTS_YES');?>
						</label>
						<label class="radio inline">
							<input type="radio" name="unsub_user" id="unsub_user2" value="0"   onclick="chaneSubsreport()" checked="checked" /><?php echo JText::_('COM_JMAILALERTS_NO');?>
						</label>
					</div>
				</div>
				<!--@TODO Selective sync
				<div class="control-group advanceoption">
					<label class="control-label" for="title" title="<?php //echo JText::_('COM_JMAILALERTS_SELECTIVE_SYNC_TOOLTIP');?>">
						<?php //echo JText::_('COM_JMAILALERTS_SELECTIVE_SYNC');?>
					</label>
					<div class="controls">
						<label class="radio inline">
							<input type="radio" name="sel_sync" id="sel_sync1" value="1" /><?php //echo JText::_('COM_JMAILALERTS_YES');?>
						</label>
						<label class="radio inline">
							<input type="radio" name="sel_sync" id="sel_sync2" value="0" checked="checked" /><?php //echo JText::_('COM_JMAILALERTS_NO');?>
						</label>
					</div>
				</div>
				-->
				<?php
				$tblclass='table table-striped table-bordered';
				?>
				<div class="row-fluid">
					<div class="span12">
						<table class="<?php echo $tblclass;?>" style="width:90% !important;">
							<tr>
								<th width="33%;">
									<?php echo JText::_('COM_JMAILALERTS_USERS');?>
								</th>
								<th width="33%;" class="center">
									<?php echo JText::_('COM_JMAILALERTS_BEFORE_SYNC');?>
									<hr class="hr hr-condensed"/>
									<?php echo JText::_('COM_JMAILALERTS_REGISTERD_USER');?> |
									<?php echo JText::_('COM_JMAILALERTS_GUEST_USER');?>
								</th>
								<th width="33%;" class="center">
									<?php echo JText::_('COM_JMAILALERTS_AFTER_SYNC');?>
									<hr class="hr hr-condensed"/>
									<?php echo JText::_('COM_JMAILALERTS_REGISTERD_USER');?> |
									<?php echo JText::_('COM_JMAILALERTS_GUEST_USER');?>
									</th>
								</th>
							</tr>
							<tr>
								<td width="33%;">
									<?php echo JText::_('COM_JMAILALERTS_CURRN_SUBSCRIBED_USERS');?>
								</td>
								<td width="33%;" class="center">
									<div class="subs_registerd subscription_report"></div>
									<span class="subs_guest" ></span>
								</td>
								<td class="center">
									<div class="subscription_report after_sync_subs_registerd"></div>
									<span class="after_sync_guest" ></span>
								</td>
							</tr>
							<tr>
								<td>
								<?php echo JText::_('COM_JMAILALERTS_CURRN_UNSUBSCRIBED_USERS');?>
								</td>
								<td width="33%;" class="center">
									<div class="unsub_registerd subscription_report" ></div>
									<span class="unsubs_guest" ></span>
								</td>
								<td class="center">
									<div class="subscription_report after_unsub_registerd"></div>
									<span class="after_unsub_guest" ></span>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo JText::_('COM_JMAILALERTS_NOT_OPTED_IN_USERS');?>
								</td>
								<td class="center">
									<div class="never_opted_in subscription_report"></div>
									<span class="" >0</span>
								</td>
								<td class="center">
									<div class="subscription_report">0</div>
									<span class="" >0</span>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php echo JText::_('COM_JMAILALERTS_USERS_TOTAL');?></strong>
								</td>
								<td class="center">
									<strong>
										<div class="column1_total subscription_report"></div>
										<span class="column2_total"></span>
									</strong>
								</td>
								<td class="center">
									<strong>
										<div class="column3_total subscription_report"></div>
										<span class="column4_total" ></span>
									</strong>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="row-fluid showhide_progressbar">
					<div class="progress progress-striped active" style="width:90%; margin-top:5%;">
						<div class="bar" style="width: <?php echo "0";?>%;">
							<b class="completed_percent" style="color:#000000;"></b>
						</div>
					</div>
				</div>
				<div class="form-actions">
					<button class="btn btn-success btn-large" type="button" onclick='sync(400,0,0);'><?php echo JText::_('COM_JMAILALERTS_SYNC_BUTTON'); ?></button>
				</div>
				<!--
					sync(400,0)
					sync paramerter 400=> is default batch size
					0 => identify that this is the first ajax request call
					0 => completed_batch_users
				-->
			</div>
			<div class="span4">
				<h5><?php echo JText::_('COM_JMAILALERTS_SYNC_NOTE');?></h5>
				<div class="alert alert-info">
					<h5><?php echo JText::_('COM_JMAILALERTS_SYNC_SYNC_NEW_USERS'); ?></h5>
					<ul>
						<li><?php echo JText::_('COM_JMAILALERTS_SYNC_SYNC_NEW_USERS_DESC'); ?></li>
					</ul>
					<h5><?php echo JText::_('COM_JMAILALERTS_SYNC_SYNC_OVERWRITE'); ?></h5>
					<ol>
						<li><?php echo JText::_('COM_JMAILALERTS_SYNC_SYNC_OVERWRITE_DESC1'); ?></li>
						<li><?php echo JText::_('COM_JMAILALERTS_SYNC_SYNC_OVERWRITE_DESC2'); ?></li>
					</ol>
				</div>
			</div>
		</div>

		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token');?>
	</form>
</div>
