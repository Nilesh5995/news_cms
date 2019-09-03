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

$k = array();
$i = 1;

$model           = $this->getModel();
$qry_concat      = $this->qry_concat;
$option          = $this->defaultoption;
$default_setting = $this->default_setting;
$altid           = $this->altid;

// $allow_user_select_plugin = $this->allow_user_select_plugin;
// For loop for alert types
for ($s = 0; $s < count($qry_concat); $s++)
{
	$plugin_data = $model->getPluginData($qry_concat[$s]);

	// Checking alert id with default selected alert for checking checkbox
	// Changed in 2.4.3 //if(in_array($altid[$s],$option))
	if (isset($option[$altid[$s]]) && isset($option[$altid[$s]]['state']) && $option[$altid[$s]]['state'] == 1)
	{
		$plugin_name         = $model->getData($altid[$s]);
		$alertchk            = "checked";
		$bstyle              = 'class="subscribed_alert"';
		$sub_status_msg      = JText::_('JMA_UNCHECK_SUB_MSG');
		$show_plugins        = "block";
		$check_hidden_plugin = 0;
	}
	else
	{
		$plugin_name         = $model->getData($altid[$s]);
		$alertchk            = "";
		$bstyle              = 'class="unsubscribed_alert"';
		$sub_status_msg      = JText::_('JMA_CHECK_SUB_MSG');
		$show_plugins        = 'none';
		$check_hidden_plugin = 1;
	}

	// Get frequency
	$altdata = $model->getFreq($altid[$s]);

	if ($altdata[3] == 1)
	{
		$allowuser = "display";
	}
	else
	{
		$allowuser = "none";
	}

	echo '
	<div>
		<div class="well">
			<div class="control-group">
				<label for="alert_' . $altid[$s] . '">
					<input type="checkbox"
						name="alt[]"
						id="alert_' . $altid[$s] . '"
						value="' . $altid[$s] . '"
						onclick="divhide(this);" ' . $alertchk . ' />
					<strong ' . $bstyle . '>' . $altdata[1] . '</strong>
					<span class="sub_status_msg">' . $sub_status_msg . '</span>
				</label>
			</div>

			<div>
				<div class="jma_alert_desc">' . $altdata[2] . '</div>
				<div id="' . $altid[$s] . '" style="display:' . $show_plugins . '">
					<div style="display:' . $allowuser . '">
						<div class="control-group">
							<div class="alert_frequncy control-label">
								<label for="c' . $altid[$s] . '"><strong>' . JText::_("CURRENT_SETTING") . '</strong></label>
							</div>

							<div class="controls">' .
								$altdata[0] .
							'</div>
						</div>';

						if ($plugin_data != false)
						{
							foreach ($plugin_data as $single_plugin_name)
							{
								$plugtitleparm = explode(':', $single_plugin_name->params);
								$plugtitltlex  = explode(',', $plugtitleparm[1]);
								$plugtitle     = str_replace('"', '', $plugtitltlex[0]);
								$flag          = 0;

								if (!empty($plugin_name))
								{
									foreach ($plugin_name as $key => $v)
									{
										if ($single_plugin_name->element == $key)
										{
											$params = implode("\n", $v);
											$params = str_replace(',', '|', $params);
											$disp   = '';
											$chk    = $v[count($v) - 1];

											if ($chk == "checked=''")
											{
												$checked = '';
											}
											else
											{
												$checked = "checked";
											}

											$flag = 1;
											break;
										}
										else
										{
											$disp    = 'style="display:none"';
											$flag    = 0;
											$checked = "";
										}
									}
								}

								if ($flag == 1)
								{
									if (!in_array($single_plugin_name->element, $k))
									{
										$k[] = $single_plugin_name->element;
										?>

										<div class="jmail-blocks">
											<div class="well jma_plugin_background">
												<?php
												if ($check_hidden_plugin)
												{
													$checked = "checked";
												}

												echo '
												<div class="jma_alert" >
													<div class="control-group">
														<label for="plg_' . $single_plugin_name->element . '_' . $altid[$s] . '">
															<input type="checkbox" name="ch' . $altid[$s] . '[]"
																id="plg_' . $single_plugin_name->element . '_' . $altid[$s] . '"
																value="' . $single_plugin_name->element . '_' . $altid[$s] . '" onclick="divhide(this);" ' . $checked . '/>
															<strong>' . JText::_($plugtitle) . '</strong>
														</label>
													</div>
												</div>';
												?>

												<div class="jmail-expands" id="<?php echo $single_plugin_name->element . '_' . $altid[$s] . $disp ?>">
													<?php
													$form = null;
													$form_path = JPATH_SITE . DS . 'plugins' . DS . 'emailalerts' . DS . $single_plugin_name->element . DS . $single_plugin_name->element . DS . 'form' . DS . 'form_' . $single_plugin_name->element . '.xml';
													$test = $single_plugin_name->element . '_' . $altid[$s];

													$form = JForm::getInstance($test, $form_path);
													$params = explode("\n", $params);

													foreach ($params as $param)
													{
														$par      = explode('=', $param);
														$par_name = $par[0];
														$par_val  = $par[1];

														if (strpos($par_val, '|'))
														{
															$array_par_val                                  = explode('|', $par_val);
															$array[$single_plugin_name->element][$par_name] = $array_par_val;
														}
														else
														{
															$array[$single_plugin_name->element][$par_name] = $par_val;
														}
													}

													$form->bind($array);

													// Iterate through the form fieldsets and display each one.
													foreach ($form->getFieldsets() as $fieldset):
														$fields = '';
														$fields = $form->getFieldset($fieldset->name);

														if (count($fields)):
														?>
															<fieldset>
																<?php
																// If the fieldset has a label set, display it as the legend.
																if (isset($fieldset->label)):
																?>
																	<legend>
																	<?php
																		echo JText::_($fieldset->label);
																	?>
																	</legend>
																<?php endif;?>

																	<?php
																	// Iterate through the fields in the set and display them.
																	foreach ($fields as $field):
																	?>
																		<div class="control-group">
																			<?php
																			// If the field is hidden, just display the input.
																			if ($field->hidden):
																			?>
																				<?php
																				$in = str_replace($single_plugin_name->element, $test, $field->input);
																				echo $in;
																				?>
																				<?php
																				else:
																				?>
																				<div class="control-label">
																					<?php
																						$in_lab = str_replace($single_plugin_name->element, $test, $field->label);
																						echo $in_lab;
																					 ?>
																					<?php
																					if (!$field->required && (!$field->type == "spacer")):
																					?>
																					<span
																						class="optional"><?php echo JText::_('COM_USERS_OPTIONAL');?></span>
																					<?php
																					endif;
																					?>
																				</div>
																				<div class="controls">
																				<?php
																					$in = str_replace($single_plugin_name->element, $test, $field->input);
																					echo $in;
																					?>
																				 </div>
																			<?php endif; ?>
																		</div>
																	<?php endforeach;?>
															</fieldset>
														<?php endif; ?>
													<?php endforeach; ?>
												</div>
												<!--jmail-expand ends-->
											</div>
											<!--well-->


										</div>
										<!--jmail-blocks-->
										<?php
									}
								}

								unset($plugtitle);
								unset($plugtitltlex);
								unset($plugtitleparm);
							}
						}
						else
						{
							echo '<div class="clearfix">&nbsp;</div>';
							echo '<div class="pull-left">' . JText::_('NO_PLUGINS_ENABLED_OR_INSTALLED') . '</div>';
							echo '<div class="clearfix">&nbsp;</div>';
						}

						echo '
						</div>
					</div>
				</div>
			</div>';

			unset($k);
			$k = array();
			$i = 1;

		echo '
		</div>
	</div>';
}
