<?php
/**
 * @version    SVN: <svn_id>
 * @package    JMailAlerts
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

$params=JComponentHelper::getParams('com_jmailalerts');
$this->private_key_cronjob=$params->get('private_key_cronjob');
$cron_masspayment='';
$cron=JRoute::_(JUri::root().'index.php?option=com_jmailalerts&view=emails&tmpl=component&task=processMailAlerts&pkey='.$this->private_key_cronjob);
?>

<script type="text/javascript">
	/*function vercheck(){
		callXML('<?php echo $this->version; ?>');
	}
	function callXML(currversion)
	{
		if (window.XMLHttpRequest){
			xhttp=new XMLHttpRequest();
		}
		else
		{
			xhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xhttp.open("GET","<?php echo JUri::base(); ?>index.php?option=com_jmailalerts&task=getVersion",false);
		xhttp.send("");
		latestver=xhttp.responseText;
		if(latestver!='')
		{
			if(currversion === latestver){
				jQuery('#newVersionChild').html('<span class="label label-success">'+'<?php echo JText::_("COM_JMAILALERTS_HAVE_LATEST_VER");?>: '+latestver+'<span>');
			}
			else{
				jQuery('#newVersionChild').html('<span class="label label-important">'+'<?php echo JText::_("COM_JMAILALERTS_NEW_VER_AVAIL");?>: '+latestver+'<span>');
			}
		}else{
			jQuery('#newVersionChild').html('<span class="label label-important">'+'<?php echo JText::_("COM_JMAILALERTS_ERROR_NEW_VERSION");?><span>');
		}
	}*/
</script>

<div class="<?php echo JMAILALERTS_WRAPPER_CLASS;?>" id="jmailalerts-dashboard">
	<form action="<?php echo JRoute::_('index.php?option=com_jmailalerts&view=subscribers'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if(!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
			<div class="row-fluid">
				<div class="span7">
					<div class="well well-small">
						<div class="row-fluid">
							<div class="span12 module-title nav-header">
								<?php echo JText::_("COM_JMAILALERTS_WELCOME_JMA");?>
								<hr class="hr-condensed"/>
							</div>
						</div>

						<div class="row-fluid">
							<div class="span3">
								<div class="icon jma_icon">
									<a class="thumbnail btn" href="index.php?option=com_jmailalerts&view=frequencies">
									<img src="<?php echo JUri::base()?>components/com_jmailalerts/assets/images/l_frequencies.png" alt="frequencies"/>
									<span><?php echo JText::_("COM_JMAILALERTS_FREQ_MENU");?></span>
									</a>
								</div>
							</div>
							<div class="span3">
								<div class="icon jma_icon">
									<a class="thumbnail btn" href="index.php?option=com_jmailalerts&view=alerts">
									<img src="<?php echo JUri::base()?>components/com_jmailalerts/assets/images/l_alerts.png" alt="alerts"/>
									<span><?php echo JText::_("COM_JMAILALERTS_ALERTS");?></span>
									</a>
								</div>
							</div>
							<div class="span3">
								<div class="icon jma_icon">
									<a class="thumbnail btn" href="index.php?option=com_jmailalerts&view=sync">
									<img src="<?php echo JUri::base()?>components/com_jmailalerts/assets/images/l_sync.png" alt="alerts"/>
									<span><?php echo JText::_("COM_JMAILALERTS_SYNC");?></span>
									</a>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="span3">
								<div class="icon jma_icon">
									<a class="thumbnail btn" href="index.php?option=com_jmailalerts&view=mailsimulate">
									<img src="<?php echo JUri::base()?>components/com_jmailalerts/assets/images/l_mailsimulate.png" alt="alerts"/>
									<span><?php echo JText::_("COM_JMAILALERTS_SIMULATE");?></span>
									</a>
								</div>
							</div>
							<div class="span3">
								<div class="icon jma_icon">
									<a class="thumbnail btn" href="index.php?option=com_jmailalerts&view=subscribers">
									<img src="<?php echo JUri::base()?>components/com_jmailalerts/assets/images/l_subscribers.png" alt="alerts"/>
									<span><?php echo JText::_("COM_JMAILALERTS_SUBS");?></span>
									</a>
								</div>
							</div>
							<div class="span3">
								<div class="icon jma_icon">
									<a class="thumbnail btn" href="index.php?option=com_jmailalerts&view=healthcheck">
									<img src="<?php echo JUri::base()?>components/com_jmailalerts/assets/images/l_healthcheck.png" alt="alerts"/>
									<span><?php echo JText::_("COM_JMAILALERTS_HELTHCHK");?></span>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="well">
						<?php
							echo JText::_('COM_JMAILALERTS_CURR_CRON_URL');

							if($this->private_key_cronjob)
							{
							?>
								<input type="text" class="input input-xxlarge" onclick="this.select();" value="<?php echo $cron;?>" aria-invalid="false">
							<?php
							}else
							{?>
								<span class="alert alert-error">
								<?php echo JText::_('COM_JMAILALERTS_ENTER_CONFIG_KEY');
							}?>
								</span>

					</div>
				</div>

				<div class="span5">
					<?php
					$versionHTML = '<span class="label label-info">' .
										JText::_('COM_JMAILALERTS_HAVE_INSTALLED_VER') . ': ' . $this->version .
									'</span>';

					if ($this->latestVersion)
					{
						if ($this->latestVersion->version > $this->version)
						{
							$versionHTML = '<div class="alert alert-error">' .
												'<i class="icon-puzzle install"></i>' .
												JText::_('COM_JMAILALERTS_HAVE_INSTALLED_VER') . ': ' . $this->version .
												'<br/>' .
												'<i class="icon icon-info"></i>' .
												JText::_("COM_JMAILALERTS_NEW_VER_AVAIL") . ': ' .
												'<span class="jma_latest_version_number">' .
													$this->latestVersion->version .
												'</span>
												<br/>' .
												'<i class="icon icon-warning"></i>' .
												'<span class="small">' .
													JText::_("COM_JMAILALERTS_LIVE_UPDATE_BACKUP_WARNING") . '
												</span>' . '
											</div>

											<div>
												<a href="index.php?option=com_installer&view=update" class="jma-btn-wrapper btn btn-small btn-primary">' .
													JText::sprintf('COM_JMAILALERTS_LIVE_UPDATE_TEXT', $this->latestVersion->version) . '
												</a>
												<a href="' . $this->latestVersion->infourl . '/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=jmailalerts&utm_content=updatedetailslink&utm_campaign=jmailalerts_ci' . '" target="_blank" class="jma-btn-wrapper btn btn-small btn-info">' .
													JText::_('COM_JMAILALERTS_LIVE_UPDATE_KNOW_MORE') . '
												</a>
											</div>';
						}
					}
					?>

					<div class="row-fluid">
						<?php if (!$this->downloadid): ?>
							<div class="">
								<div class="clearfix pull-right">
									<div class="alert alert-warning">
										<?php echo JText::sprintf('COM_JMAILALERTS_LIVE_UPDATE_DOWNLOAD_ID_MSG', '<a href="https://techjoomla.com/about-tj/faqs/#how-to-get-your-download-id" target="_blank">' . JText::_('COM_JMAILALERTS_LIVE_UPDATE_DOWNLOAD_ID_MSG2') . '</a>'); ?>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<div class="">
							<div class="clearfix pull-right">
								<?php echo $versionHTML; ?>
							</div>
						</div>
					</div>

					<div class="clearfix">&nbsp;</div>
					<div class="well well-small">
						<div class="module-title nav-header">
							<?php
							if(JVERSION >= '3.0')
								echo '<i class="icon-mail-2"></i>';
							else
								echo '<i class="icon-envelope"></i>';
							?> <strong><?php echo JText::_('COM_JMAILALERTS'); ?></strong>
						</div>
						<hr class="hr-condensed"/>

						<div class="row-fluid">
							<div class="span12 alert alert-success"><?php echo JText::_('COM_JMAILALERTS_INTRO'); ?></div>
						</div>

						<div class="row-fluid">
							<div class="span12">
								<p class="pull-right"><span class="label label-info"><?php echo JText::_('COM_JMAILALERTS_LINKS'); ?></span></p>
							</div>
						</div>

						<div class="row-striped">
							<div class="row-fluid">
								<div class="span12">
									<a href="https://techjoomla.com/table/extension-documentation/documentation-for-jmailalerts/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=jmailalerts&utm_content=textlink&utm_campaign=jmailalerts_ci" target="_blank"><i class="icon-file"></i> <?php echo JText::_('COM_JMAILALERTS_DOCS');?></a>
								</div>
							</div>
							<div class="row-fluid">
								<div class="span12">
									<a href="https://techjoomla.com/support-tickets/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=jmailalerts&utm_content=textlink&utm_campaign=jmailalerts_ci" target="_blank">
										<?php
										if(JVERSION >= '3.0')
											echo '<i class="icon-support"></i>';
										else
											echo '<i class="icon-user"></i>';
										?> <?php echo JText::_('COM_JMAILALERTS_TECHJOOMLA_SUPPORT_CENTER'); ?></a>
								</div>
							</div>
							<div class="row-fluid">
								<div class="span12">
									<a href="http://extensions.joomla.org/extensions/extension/marketing/mailing-a-distribution-lists/j-mailalerts" target="_blank">
										<?php
										if(JVERSION >= '3.0')
											echo '<i class="icon-quote"></i>';
										else
											echo '<i class="icon-bullhorn"></i>';
										?> <?php echo JText::_('COM_JMAILALERTS_LEAVE_JED_FEEDBACK'); ?></a>
								</div>
							</div>
						</div>

						<br/>
						<div class="row-fluid">
							<div class="span12">
								<p class="pull-right">
									<span class="label label-info"><?php echo JText::_('COM_JMAILALERTS_STAY_TUNNED'); ?></span>
								</p>
							</div>
						</div>

						<div class="row-striped">
							<div class="row-fluid">
								<div class="span4"><?php echo JText::_('COM_JMAILALERTS_FACEBOOK'); ?></div>
								<div class="span8">
									<!-- facebook button code -->
									<div id="fb-root"></div>
									<script>(function(d, s, id) {
									  var js, fjs = d.getElementsByTagName(s)[0];
									  if (d.getElementById(id)) return;
									  js = d.createElement(s); js.id = id;
									  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
									  fjs.parentNode.insertBefore(js, fjs);
									}(document, 'script', 'facebook-jssdk'));</script>
									<div class="fb-like" data-href="https://www.facebook.com/techjoomla" data-send="true" data-layout="button_count" data-width="250" data-show-faces="false" data-font="verdana"></div>
								</div>
							</div>

							<div class="row-fluid">
								<div class="span4"><?php echo JText::_('COM_JMAILALERTS_TWITTER'); ?></div>
								<div class="span8">
									<!-- twitter button code -->
									<a href="https://twitter.com/techjoomla" class="twitter-follow-button" data-show-count="false">Follow @techjoomla</a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
								</div>
							</div>

							<div class="row-fluid">
								<div class="span4"><?php echo JText::_('COM_JMAILALERTS_GPLUS'); ?></div>
								<div class="span8">
									<!-- Place this tag where you want the +1 button to render. -->
									<div class="g-plusone" data-annotation="inline" data-width="300" data-href="https://plus.google.com/102908017252609853905"></div>
									<!-- Place this tag after the last +1 button tag. -->
									<script type="text/javascript">
									(function() {
									var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
									po.src = 'https://apis.google.com/js/plusone.js';
									var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
									})();
									</script>
								</div>
							</div>
						</div>

						<br/>
						<div class="row-fluid">
							<div class="span12 center">
								<?php
								$logo_path='<img src="'.JUri::base().'components/com_jmailalerts/assets/images/techjoomla.png" alt="Techjoomla" class="jma_vertical_align_top"/>';
								?>
								<a href='https://techjoomla.com/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=jmailalerts&utm_content=logolink&utm_campaign=jmailalerts_ci' target='_blank' alt="Techjoomla">
									<?php echo $logo_path;?>
								</a>
								<p><?php echo JText::sprintf('COM_JMAILALERTS_COPYRIGHT', date('Y')); ?></p>
							</div>
						</div>
					</div>
				</div><!--END span4 -->
			</div>
			<!--END outermost row-fluid -->
		</div>
	</form>
</div>
