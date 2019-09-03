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

JHtml::_('behavior.modal', 'a.modal');
?>

<script>
	function validate_form(){
		// Chek if alert, userid and the email address is entered
		if(document.getElementById('user_id_box').value == '' || document.getElementById('send_mail_to_box').value == '') {
			alert("<?php echo JText::_('COM_JMAILALERTS_SIMULATE_VALIDATION_MSG'); ?>");
			return 0;
		}
		else {
			return 1;
		}
	}

	function submit_this_form(simulation_form){
		simulation_form.submit();
	}

	function previewMail()
	{
		let simulationLink= "<?php echo JUri::base() . 'index.php?option=com_jmailalerts&controller=mailsimulate&task=simulate&tmpl=component&send_mail_to_box=admin@admin.com&flag=1&user_id_box='; ?>"
		let userid = document.getElementById('user_id_box').value;
		let sdate  = document.getElementById('select_date_box').value;

		let alertname  = document.getElementById('altypename').value;
		simulationLink = simulationLink + userid + "&select_date_box=" + sdate + "&altypename=" + alertname;
		document.getElementById('linkforsimulate').setAttribute('href', simulationLink);
	}
</script>

<div class="<?php echo JMAILALERTS_WRAPPER_CLASS;?>" id="jmailalerts-mailsimulate">
	<form action="" method="POST" name="simulation_form" ENCTYPE="multipart/form-data" id="simulation_form" class="form-horizontal">
	<?php if(!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
			<div class="control-group">
				<div class="control-label">
					<label for="altypename"><?php echo JText::_("COM_JMAILALERTS_SELECT_ATYPE"); ?></label>
				</div>
				<div class="controls"><?php echo $this->alertname; ?></div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<label for="user_id_box"><?php echo JText::_("COM_JMAILALERTS_USER_ID"); ?></label>
				</div>
				<div class="controls">
					<input type="text" width="20" size="20" maxlength="20" value="" name = "user_id_box" id="user_id_box" />
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<label for="send_mail_to_box"><?php echo JText::_("COM_JMAILALERTS_SEND_MAIL_TO"); ?></label>
				</div>
				<div class="controls">
					<input type="text" width="20" size="20" maxlength="40" value="" name = "send_mail_to_box" id="send_mail_to_box" />
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_('');?>
					<label for="select_date_box"><?php echo JText::_("COM_JMAILALERTS_SELECT_DATE"); ?></label>
				</div>
				<div class="controls">
					<?php echo JHtml::_('calendar'
						, date('')
						, 'select_date_box'
						, 'select_date_box'
						, '%Y-%m-%d '
						, array('class' => 'inputbox', 'size' => '20', 'maxlength' => '19','name' => 'select_date_box','id' => 'select_date_box')
					);?>
				</div>
			</div>

			<div>
				<button type="button" class="btn btn-large btn-success" id="simulate_button"
					onclick=" if(validate_form()) { submit_this_form(this.form); }">
						<?php echo JText::_('COM_JMAILALERTS_SIMULATE');?>
				</button>
				&nbsp;&nbsp;&nbsp;
				<a id ="linkforsimulate" rel="{handler: 'iframe', size: {x:700, y: 600}}" onclick ="previewMail();" href= "<?php echo JURI::base();?>" class='modal'>
				<input id="previewBtn" class="btn btn-large btn-info validate" type="button" value="<?php echo JText::_('COM_JMAILALERTS_PREVIEW'); ?>">
				</a>
			</div>

			<input type="hidden" name="option" value="com_jmailalerts" />
			<input type="hidden" name="task" value="simulate" />
			<input type="hidden" name="controller" value="mailsimulate" />
		</div>
	</form>
</div>
