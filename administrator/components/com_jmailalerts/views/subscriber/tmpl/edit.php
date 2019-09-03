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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>

<script type="text/javascript">
	js = techjoomla.jQuery.noConflict();
	js(document).ready(function(){
		var userid=techjoomla.jQuery('#jform_user_id').val();
		CheckBoxCheck(userid);
	});

	Joomla.submitbutton = function(task)
	{
		if(task == 'subscriber.cancel'){
			Joomla.submitform(task, document.getElementById('subscriber-form'));
		}
		else{
			if (task != 'subscriber.cancel' && document.formvalidator.isValid(document.id('subscriber-form'))) {
				Joomla.submitform(task, document.getElementById('subscriber-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
	/**
	Method guest check box is yes then userid=0 & it will be readonly
	*/
	function Guest_User_Check()
	{
		if(document.adminForm.gues_user_chk.checked===true)
		{
			techjoomla.jQuery('#jform_user_id').val('0');
			techjoomla.jQuery('#jform_user_id').attr("readonly",true);
		}
		else
		{
			techjoomla.jQuery('#jform_user_id').val('');
			techjoomla.jQuery('#jform_user_id').attr("readonly",false);
		}
	}
	/**
	Method used when editing guest user data
	If user is guest user id=0 ,checkbox guest user should be check
	*/
	function CheckBoxCheck(userid)
	{
		if(userid==0)
		{
			document.adminForm.gues_user_chk.checked=true;
			techjoomla.jQuery('#jform_user_id').val('0');
			techjoomla.jQuery('#jform_user_id').attr("readonly",true);
		}
		else
		{
			document.adminForm.gues_user_chk.checked=false;
			techjoomla.jQuery('#jform_user_id').attr("readonly",false);
		}
	}
</script>

<div class="<?php echo JMAILALERTS_WRAPPER_CLASS;?>" id="jmailalerts-subscriber">
	<form action="<?php echo JRoute::_('index.php?option=com_jmailalerts&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="subscriber-form" class="form-validate">
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
					</div>
					<div class="control-group">
						<label class="control-label" for="gues_user_chk" title="<?php echo JText::_('COM_JMAILALERTS_GUEST_USER_SUBSCRIPTION_TOOLTIP');?>">
							<?php echo JText::_('COM_JMAILALERTS_GUEST_USER_SUBSCRIPTION');?>
						</label>
						<div class="controls">
							<input type="checkbox" name="gues_user_chk" id="gues_user_chk" value="1" onchange="Guest_User_Check()"/>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('email_id'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('email_id'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('alert_id'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('alert_id'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('frequency'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('frequency'); ?></div>
					</div>
					<input type="hidden" name="jform[date]" value="<?php echo $this->item->date; ?>" />
					<input type="hidden" name="jform[plugins_subscribed_to]" value="<?php echo $this->item->plugins_subscribed_to; ?>" />
				</fieldset>
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
