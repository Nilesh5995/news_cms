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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.framework', true);

// Bootstrap tooltip and chosen js
JHtml::_('bootstrap.tooltip');

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root(true) . '/components/com_jmailalerts/assets/css/jmailalerts.css');
$doc->addStyleDeclaration('.ui-accordion-header {margin: 1px 0px !important}');

$js='
	function divhide(thischk)
	{
		if(thischk.checked){
			document.getElementById(thischk.value).style.display="block";
		}
		else{
			document.getElementById(thischk.value).style.display="none";
		}
	}

	function divhide1(thischk)
	{
		if(thischk.value==0){
			document.getElementById("ac").style.display="none";
		}
		else{
			document.getElementById("ac").style.display="block";
		}
	}
';

$doc->addScriptDeclaration($js);
?>

<?php
// Added in 2.4.3
// Newly added for JS toolbar inclusion
if (JFolder::exists(JPATH_SITE . '/components/com_community') && $this->params->get('jstoolbar') == '1')
{
	$jsFile = JPATH_ROOT . '/components/com_community/libraries/toolbar.php';

	if (JFile::exists($jsFile))
	{
		require_once $jsFile;
		$toolbar = CFactory::getToolbar();
		$tool    = CToolbarLibrary::getInstance();
		?>
		<div id="community-wrap">
			<?php
				echo $tool->getHTML();
			?>
		</div>
		<?php
	}
}
// Eoc for JS toolbar inclusion
?>

<!--div for registration of guest user.-->
<div class="<?php echo JMAILALERTS_WRAPPER_CLASS;?> well jma_plugin_background" id="jmailalerts-emails">
	<div class="col100" id="e-mail_alert">
		<?php if ($this->page_title):?>
			<div class="componentheading page-header">
				<h2><?php echo $this->page_title;?></h2>
			</div>
		<?php endif;?>

		<form action="" class="form-validate form-horizontal" method="POST" id="adminform" name="adminform" ENCTYPE="multipart/form-data">
			<?php
			// if enable guest user registration then show name and email field.
			if (!$this->user->id && $this->params->get('guest_subcription')==1)
			{
				?>
				<div class="row-fluid" ><!--1-->
					<div class="span8"><!--2-->
						<div class="well">
							<div class="page-header">
								<h2><?php echo JText::_('COM_JMAILALERT_USER_REG');	?> </h2>
								<?php echo JText::_('COM_JMAILALERT_UN_REGISTER');?>
							</div>
							<div class="control-group">
								<label class="control-label"  for="user_name">
									<?php echo JText::_( 'COM_JMAILALERT_USER_NAME' ); ?>
								</label>
								<div class="controls">
									<input class="inputbox required validate-name" type="text" name="user_name" id="user_name" size="30" maxlength="50" value="" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label"  for="user_email">
									<?php echo JText::_( 'COM_JMAILALERT_USER_EMAIL' ); ?>
								</label>
								<div class="controls">
									<input class="inputbox required validate-email" type="text" name="user_email" id="user_email" size="30" maxlength="100" value="" />
								</div>
							</div>
						</div>
					</div>
					<div class="span4">
						<div class="well">
							<div class="page-header">
								<h2><?php echo JText::_('COM_JMAILALERT_LOGIN');?> </h2>
								<?php echo JText::_('COM_JMAILALERT_REGISTER');?>
							</div>
							<a href='<?php
								$msg = JText::_('LOGIN');

								// Get current url.
								$current = JUri::getInstance()->toString();
								$url     = base64_encode($current);
								echo JRoute::_('index.php?option=com_users&view=login&return=' . $url, false); ?>'>
								<div style="margin-left:auto;margin-right:auto;" class="control-group">
									<input id="LOGIN" class="btn btn-large btn-success validate" type="button" value="<?php echo JText::_('COM_JMAILALERT_SIGN_IN'); ?>">
								</div>
							</a>
						</div>
					</div>
				</div>
				<?php
				}
				elseif(!$this->user->id && $this->params->get('guest_subcription')==0)
				{
					?>
					<div class="alert alert-block">
						<?php echo JText::_('YOU_NEED_TO_BE_LOGGED_IN'); ?>
					</div>
				</div><!--Techjoomla bootstrap ends if not logged in-->
			</div><!--Mail_alert ends if not logged in-->
					<?php
					return false;
				}

			// Take Component parameter as no config file present now.
			if($this->params->get('intro_msg') != '')
			{
				?>
				<div class="jma_email_intro">
					<h4>
						<?php echo JText::_($this->params->get('intro_msg'));?>
					</h4>
				</div>
				<?php
			}

			$disp_none = " ";

			if (trim($this->cntalert) == 0)
			{
				$disp_none = "display:none";
			}
			?>

			<table class="jma_table">
				<tr>
					<td>

					</td>
				</tr>
				<tr>
					<td>
						<?php $maplist[] = JHTML::_('select.option', '0', JText::_('N0_FREQUENCY'), 'value', 'text'); ?>
						<div id="ac" style="<?php echo $disp_none;?>">
							<?php
							if (trim($this->cntalert) != 0) {
									echo $this->loadTemplate('joomla16');
							}
							?>
						</div>
					</td>
				</tr>
			</table>

			<div id="manual_div">
				<?php
				if (trim($this->cntalert) != 0)
				{
					?>
					<div class="form-actions">
					<button class="btn btn-primary validate" type="submit" ><?php echo JText::_('BUTTON_SAVE'); ?></button>
					</div>
					<?php
				}
				?>

				<input type="hidden" name="option" value="com_jmailalerts">
				<input type="hidden" id="task" name="task" value="savePref">
			</div>
		</form>
	</div>
</div>
