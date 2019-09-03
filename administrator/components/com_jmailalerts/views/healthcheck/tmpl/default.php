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

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base() . 'components/com_jmailalerts/assets/css/jmailalerts.css');
?>

<div class="<?php echo JMAILALERTS_WRAPPER_CLASS;?>" id="jmailalerts-healthcheck">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php if(!empty($this->sidebar)): ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
		<?php else : ?>
			<div id="j-main-container">
		<?php endif;?>

		<table class="table table-striped">
			<thead>
				<tr>
					<th width="50">
						<?php echo JText::_('COM_JMAILALERTS_HEALTHCHECK_ID'); ?>
					</th>
					<th align="center" width="300">
						<?php echo JText::_('COM_JMAILALERTS_CHECK'); ?>
					</th>
					<th width="300">
						<?php echo JText::_('COM_JMAILALERTS_DESC'); ?>
					</th>
					<th width="300">
						<?php echo JText::_('COM_JMAILALERTS_STATUS'); ?>
					</th>
					<th nowrap="nowrap">
						<?php echo JText::_('COM_JMAILALERTS_ACT_NEED'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<?php
					$installed = (int) (!empty($this->data['installed'])) ? 1 : 0;
					$class     = $installed ? 'jma-green' : 'jma-red';
					?>

					<td align="center">
						<?php echo "1" ?>
					</td>

					<td>
						<?php echo JText::_('COM_JMAILALERTS_QUESTION1'); ?>
					</td>

					<td> </td>

					<td>
						<span class="<?php echo $class; ?>">
							<?php
							if ($installed)
							{
								echo JText::_('JYES');
							}
							else
							{
								echo JText::_('JNO');
							}
							?>
						</span>
					</td>

					<td align="left">
						<?php
						if ($installed)
						{
							echo JText::_('COM_JMAILALERTS_NO_ACT');
						}
						else
						{
							echo JText::_('COM_JMAILALERTS_ACT1');
						}
						?>
					</td>
				</tr>

				<tr>
					<?php
					$enabled = (int) (!empty($this->data['enable'])) ? 1 : 0;
					$class     = $enabled ? 'jma-green' : 'jma-red';
					?>
					<td align="center">
						<?php echo "2"; ?>
					</td>

					<td>
						<?php echo JText::_( 'COM_JMAILALERTS_QUESTION2' ); ?>
					</td>

					<td>
					</td>

					<td>
						<span class="<?php echo $class; ?>">
							<?php
							if ($enabled)
							{
								echo JText::_('JYES');
							}
							else
							{
								echo JText::_('JNO');
							}
							?>
						</span>
					</td>

					<td align="left">
						<?php
						if ($enabled)
						{
							echo JText::_( 'COM_JMAILALERTS_NO_ACT' );
						}
						else
						{
							?>
							<a href="index.php?option=com_plugins" target="_blank">
								<input id="LOGIN" class="btn btn-large btn-info validate" type="button" value="<?php echo JText::_('COM_JMAILALERTS_PLG_MANAGER'); ?>">
							</a>
							<?php
						}
						?>
					</td>
				</tr>

				<tr>
					<td align="center">
					   <?php echo "3"; ?>
					</td>

					<td>
						<?php echo JText::_('COM_JMAILALERTS_QUESTION3'); ?>
					</td>

					<td>
						<?php echo JText::_('COM_JMAILALERTS_DESC_Q3'); ?>
					 </td>

					<td class="small">
						<?php
						$warn = 0;
						$lang = JFactory::getLanguage();

						foreach ($this->plugins_name as $plgname)
						{
							$lang->load("plg_emailalerts_" . $plgname->element, JPATH_ADMINISTRATOR);

							$plgnm = $plgname->enabled;

							if (!$plgnm)
							{
								?>
								<span class="jma-red">
									<?php
									echo JText::_($plgname->name);
									$warn = 1;
									?>
								</span>
								<?php
							}
							else
							{
								echo JText::_($plgname->name);
							}

							echo "<br />";
						}
						?>
					</td>

					<td align="left">
						<?php
						$cnt = 0;

						foreach ($this->plugins_name as $plgname)
						{
							$plgnm = $plgname->enabled;
							$cnt   = ($plgnm == 0) ? $cnt + 1 : $cnt;
						}

						$plgname = (count($this->plugins_name) == $cnt) ? 0 : 1;

						if ($plgname == 1 && $warn == 0)
						{
							echo JText::_('COM_JMAILALERTS_NO_ACT');
						}
						else
						{
							echo JText::_('COM_JMAILALERTS_ACT3');
							?>

							<br />

							<a href="index.php?option=com_jmailalerts&view=alerts" target="_blank">
								<input id="manage-alerts" class="btn btn-info validate" type="button" value="<?php echo JText::_('COM_JMAILALERTS_MANAGE_ALERT'); ?>">
							</a>

							<br/>
							<br />

							<a href="index.php?option=com_plugins&filter[folder]=emailalerts" target="_blank">
								<input id="manage-plugins" class="btn btn-info validate" type="button" value="<?php echo JText::_('COM_JMAILALERTS_PLG_MANAGER'); ?>">
							</a>
							<?php
						}
						?>
					</td>
				</tr>

				<tr>
					<?php
					$alerts = (int) (!empty($this->data['alerts'])) ? 1 : 0;
					$class     = $alerts ? 'jma-green' : 'jma-red';
					?>

					<td align="center">
						<?php echo "4" ?>
					</td>

					<td>
						<?php echo JText::_('COM_JMAILALERTS_QUESTION4'); ?>
					</td>

					<td> </td>

					<td>
						<span class="<?php echo $class; ?>">
							<?php
							if ($alerts)
							{
								echo JText::sprintf('COM_JMAILALERTS_TOTAL_N_ALERTS_FOUND', $this->data['alerts']);
							}
							else
							{
								echo JText::_('JNO');
							}
							?>
						</span>
					</td>

					<td align="left">
						<?php
						if ($alerts)
						{
							echo JText::_('COM_JMAILALERTS_NO_ACT');
						}
						else
						{
							?>
							<a href="index.php?option=com_jmailalerts&view=alerts" target="_blank">
								<input id="LOGIN" class="btn btn-info validate" type="button" value="<?php echo JText::_('COM_JMAILALERTS_MANAGE_ALERT'); ?>">
							</a>
							<?php
						}
						?>
					</td>
				</tr>

				<tr>
					<?php
					$published = (int) (!empty($this->data['published'])) ? 1 : 0;
					$class     = $published ? 'jma-green' : 'jma-red';
					?>

					<td align="center">
					   <?php echo "5" ?>
					</td>

					<td>
						<?php echo JText::_('COM_JMAILALERTS_QUESTION5'); ?>
					</td>

					<td> </td>

					<td>
						<span class="<?php echo $class; ?>">
							<?php
							if ($published)
							{
								echo JText::sprintf('COM_JMAILALERTS_TOTAL_N_ALERTS_PUBLISHED', $this->data['published']);
							}
							else
							{
								echo JText::_('JNO');
							}
							?>
						</span>
					</td>

					<td align="left">
						<?php
						if ($published)
						{
							echo JText::_( 'COM_JMAILALERTS_NO_ACT' );
						}
						else
						{
							?>
							<a href="index.php?option=com_jmailalerts&view=alerts" target="_blank">
								<input id="LOGIN" class="btn btn-info validate" type="button" value="<?php echo JText::_('COM_JMAILALERTS_MANAGE_ALERT'); ?>">
							</a>
							<?php
						}
						?>
					</td>
				</tr>

				<tr>
					<?php
					$defaults = (int) (!empty($this->data['defaults'])) ? 1 : 0;
					$class   = $defaults ? 'jma-green' : 'jma-red';
					?>
					<td align="center">
						<?php echo "6" ?>
					</td>

					<td>
						<?php echo JText::_('COM_JMAILALERTS_QUESTION6'); ?>
					</td>

					<td>
						<?php echo JText::_('COM_JMAILALERTS_DESC_Q6'); ?>
					</td>

					<td>
						<span class="<?php echo $class; ?>">
							<?php
							if ($defaults)
							{
								echo JText::sprintf('COM_JMAILALERTS_TOTAL_N_ALERTS_DEFAULT', $this->data['defaults']);
							}
							else
							{
								echo JText::_('JNO');
							}
							?>
						</span>
					</td>

					<td align="left">
						<?php
						if ($defaults)
						{
							echo JText::_( 'COM_JMAILALERTS_NO_ACT' );
						}
						else
						{
							?>
							<a href="index.php?option=com_jmailalerts&view=alerts" target="_blank">
								<input id="jma-defaults" class="btn btn-info validate" type="button" value="<?php echo JText::_('COM_JMAILALERTS_MANAGE_ALERT'); ?>">
							</a>
							<?php
						}
						?>
					</td>
				</tr>

				<tr>
					<?php
					$synced = (int) (!empty($this->data['synced'])) ? 1 : 0;
					$class  = $synced ? 'jma-green' : 'jma-red';
					?>
					<td align="center">
					   <?php echo "7" ?>
					</td>

					<td>
						<?php echo JText::_( 'COM_JMAILALERTS_QUESTION7' ); ?>
					</td>

					<td> </td>

					<td>
						<span class="<?php echo $class; ?>">
							<?php
							if ($synced)
							{
								echo JText::sprintf('COM_JMAILALERTS_TOTAL_N_ALERTS_SYNCED', $this->data['synced']);
							}
							else
							{
								echo JText::_('JNO');
							}
							?>
						</span>
					</td>

					<td align="left">
						<?php
						if ($synced)
						{
							echo JText::_( 'COM_JMAILALERTS_NO_ACT' );
						}
						else
						{
							?>
							<a href="index.php?option=com_jmailalerts&view=sync" target="_blank">
								<input id="LOGIN" class="btn btn-info validate" type="button" value="<?php echo JText::_('COM_JMAILALERTS_SYNC_MAIL'); ?>">
							</a>
							<?php
						}
						?>
					</td>
				</tr>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="9">
						<?php // @echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>

		<input type="hidden" name="option" value="com_jmailalerts" />
		<input type="hidden" name="view" value="healthcheck" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="healthcheck" />
	</form>
</div>
