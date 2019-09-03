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

// Import CSS
$conf = JFactory::getConfig();
$editor_name = $conf->get('editor');
$input=JFactory::getApplication()->input;
$id=$input->get('id','','INT');
?>

<script type="text/javascript">
	techjoomla.jQuery(document).ready(function(){
		<?php if(!$id) {?>

			setTimeout(function(){/*time used to avoid blank editor area when creating new alert*/
				techjoomla.jQuery.ajax({
					url: "index.php?option=com_jmailalerts&task=loadtemplate",
					type:"GET",
					dataType:"json",
					success:function(data)
					{
							var editor="<?php echo $editor_name;?>";

							if(editor=='tinymce' ||  editor=='jce')
							{
								techjoomla.jQuery("iframe").contents().find("body#tinymce").html(data['template']);
							}
							else if (editor == 'none' )
							{
								techjoomla.jQuery('textarea[name="jform[template]"]').val(data['template']);
							}
							else
							{
								techjoomla.jQuery("iframe").contents().find("body").html(data['template']); //cke_show_borders
							}
							// chnage text area value
							//techjoomla.jQuery("#data_message_body").val(data['template']); //data[message_body]
							techjoomla.jQuery("#jform_template_css").val(data['css']);
					}
				});
			},2000);

		<?php } ?>
	});
	Joomla.submitbutton = function(task)
	{
		if(task == 'alert.cancel'){
			Joomla.submitform(task, document.getElementById('alert-form'));
		}
		else{
			if (task != 'alert.cancel' && document.formvalidator.isValid(document.id('alert-form'))) {
				var allowed_frequencies=techjoomla.jQuery('#jform_allowed_freq').val(); //get array of allowed frequencies
				var default_freq=techjoomla.jQuery('#jform_default_freq').val(); //get the default frequency
				var valid_default_freq=allowed_frequencies.indexOf(default_freq); //check default frequency exist in allowed frequency

				if(valid_default_freq>=0) //check default frequency exist in allowed frequency
				{
					Joomla.submitform(task, document.getElementById('alert-form'));
				}
				else
				{
					alert('Please select only default frquency which is in selected allowed frequencies');
					return false;
				}
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<div class="<?php echo JMAILALERTS_WRAPPER_CLASS;?>" id="jmailalerts-alert">
	<form action="<?php echo JRoute::_('index.php?option=com_jmailalerts&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="alert-form" class="form-horizontal form-validate">
		<div class="row-fluid">
			<div class="span7">
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
						<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('email_subject'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('email_subject'); ?></div>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php echo JText::_('COM_JMAILALERTS_FORM_LBL_ALERT_TEMPLATE');?></legend>
					<!--
					<div class="control-label"><?php echo $this->form->getLabel('template'); ?></div>
					-->
					<div class="control-group"><?php echo $this->form->getInput('template'); ?></div>
				</fieldset>
			</div>

			<div class="span5">
				<fieldset class="adminform">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('allow_users_select_plugins'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('allow_users_select_plugins'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('respect_last_email_date'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('respect_last_email_date'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('is_default'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('is_default'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('allowed_freq'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('allowed_freq'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('default_freq'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('default_freq'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('enable_batch'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('enable_batch'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('batch_size'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('batch_size'); ?></div>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php echo JText::_('COM_JMAILALERTS_FORM_LBL_ALERT_TEMPLATE_CSS');?></legend>
					<p class='text text-info small'><?php echo JText::_('COM_JMAILALERTS_CSS_EDITOR_MSG');?></p>
					<!--
					<div class="control-label "><?php echo $this->form->getLabel('template_css'); ?></div>
					-->
					<div class="control-group"><?php echo $this->form->getInput('template_css'); ?></div>
				</fieldset>

				<fieldset>
					<legend><?php echo JText::_('COM_JMAILALERTS_TAGS_LIST');?></legend>
					<p class="text text-info small"><?php echo JText::_('COM_JMAILALERTS_TAGS_LIST_INFO'); ?></p>

					<div class="control-group">
						<?php
						if (count($this->item->email_alert_plugin_names))
						{
							echo "<hr class='hr hr-condensed'/>";
							echo "<p class='text text-info'>" . JText::_('COM_JMAILALERTS_JMA_PLUGINS_TAGS') . "</p>";

							// This code echoes the plugin 'tags' on the right side of the config
							// Set index to 0
							$i = 0;
							$lang = JFactory::getLanguage();

							foreach ($this->item->email_alert_plugin_names as $email_alert_plugin_name)
							{
								echo "<hr class='hr hr-condensed'/>";

								$lang->load("plg_emailalerts_" . $email_alert_plugin_name, JPATH_ADMINISTRATOR);

								echo '[' . $email_alert_plugin_name . ']
								<p class="small">' . JText::_($this->item->plugin_description_array[$i++]) . '</p>';
							}

							echo "<hr class='hr'/>";
						}
						?>

						<div class="">[NAME]
							<p class="small"><?php echo JText::_('COM_JMAILALERTS_NAME_OF_RECIVER');?></p>
						</div>

						<div class="">[SITENAME]
							<p class="small"><?php echo JText::_('COM_JMAILALERTS_SITE_NAME');?></p>
						</div>

						<div class="">[SITELINK]
							<p class="small"><?php echo JText::_('COM_JMAILALERTS_SITE_LINK');?></p>
						</div>

						<div class="">[PREFRENCES]
							<p class="small"><?php echo JText::_('COM_JMAILALERTS_PREF_LINK');?></p>
						</div>

						<div class="">[mailuser]
							<p class="small"><?php echo JText::_('COM_JMAILALERTS_EMAIL_SUBS');?></p>
						</div>
					</div>
				<fieldset>
			</div>

			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
