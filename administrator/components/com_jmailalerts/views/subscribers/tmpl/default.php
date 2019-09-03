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
JHtml::_('behavior.modal', 'a.modal');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_jmailalerts');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_jmailalerts&task=subscribers.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'subscriberList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

	function preview(uid,sdate,aid,rid,email)
	{
		let flag = 0;
		let link= "<?php echo JUri::base() . 'index.php?option=com_jmailalerts&task=preview&tmpl=component&send_mail_to_box=admin@admin.com&flag=1&userid='; ?>"

		link = link+"&user_id="+uid+"&select_date_box="+sdate+"&alert_id="+aid+"&email_id="+email;

		document.getElementById(rid).setAttribute('href',link);
	}
</script>

<?php
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
?>

<div class="<?php echo JMAILALERTS_WRAPPER_CLASS;?>" id="jmailalerts-subscribers">
	<form action="<?php echo JRoute::_('index.php?option=com_jmailalerts&view=subscribers'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if(!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
			<!--Start Filter & seatch button code for joomla 3.0-->
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible">
						<?php echo JText::_('JSEARCH_FILTER');?>
					</label>
					<input type="text" name="filter_search" id="filter_search"
					placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
					value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
					title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
				</div>

				<div class="btn-group pull-left">
					<button class="btn hasTooltip" type="submit"
						title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
							<i class="icon-search"></i>
					</button>

					<button class="btn hasTooltip" type="button"
						title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
						onclick="document.id('filter_search').value=''; this.form.submit();">
							<i class="icon-remove"></i>
					</button>
				</div>

				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible">
						<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>

				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible">
						<?php echo JText::_('JFIELD_ORDERING_DESC');?>
					</label>
					<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
						<option value="asc"
							<?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>>
							<?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?>
						</option>
						<option value="desc"
							<?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>>
							<?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?>
						</option>
					</select>
				</div>

				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible">
						<?php echo JText::_('JGLOBAL_SORT_BY');?>
					</label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
					</select>
				</div>
			</div>
			<div class="clearfix"> </div>

			<?php if (empty($this->items)): ?>
				<div class="alert alert-no-items">
					<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php else: ?>
				<table class="table table-striped" id="subscriberList">
					<thead>
						<tr>
							<?php if (isset($this->items[0]->ordering)): ?>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
							</th>
							<?php endif; ?>

							<th width="1%" class="hidden-phone">
								<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>

							<?php if (isset($this->items[0]->state)): ?>
								<th width="1%" class="nowrap center">
									<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>

							<th class='left'>
								<?php echo JHtml::_('grid.sort',  'COM_JMAILALERTS_SUBSCRIBERS_USER_ID', 'a.user_id', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo JHtml::_('grid.sort',  'COM_JMAILALERTS_SUBSCRIBERS_ALERT_ID', 'a.alert_id', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo JHtml::_('grid.sort',  'COM_JMAILALERTS_SUBSCRIBERS_FREQUENCY', 'a.frequency', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo JHtml::_('grid.sort',  'COM_JMAILALERTS_SUBSCRIBERS_DATE', 'a.date', $listDirn, $listOrder); ?>
							</th>
							<th>
								<?php echo JText::_('COM_JMAILALERTS_PREVIEW'); ?>
							</th>

							<?php if (isset($this->items[0]->id)): ?>
								<th width="1%" class="nowrap center hidden-phone">
									<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>
						</tr>
					</thead>

					<tfoot>
						<?php
						if (isset($this->items[0]))
						{
							$colspan = count(get_object_vars($this->items[0]));
						}
						else
						{
							$colspan = 10;
						}
						?>
						<tr>
							<td colspan="<?php echo $colspan ?>">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>

					<tbody>
						<?php
						foreach ($this->items as $i => $item) :
							$ordering  = ($listOrder == 'a.ordering');
							$canCreate = $user->authorise('core.create', 'com_jmailalerts');
							$canEdit   = $user->authorise('core.edit', 'com_jmailalerts');
							$canCheckin = $user->authorise('core.manage', 'com_jmailalerts');
							$canChange = $user->authorise('core.edit.state', 'com_jmailalerts');
							?>

							<tr class="row<?php echo $i % 2; ?>">
								<?php
								if (isset($this->items[0]->ordering)): ?>
									<td class="nowrap center hidden-phone">
										<?php
										if ($canChange):
										$disableClassName = '';
										$disabledLabel	  = '';

										if (!$saveOrder) :
										$disabledLabel    = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
										endif; ?>

										<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
											<i class="icon-menu"></i>
										</span>

										<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
										<?php else : ?>
											<span class="sortable-handler inactive" >
												<i class="icon-menu"></i>
											</span>
										<?php endif; ?>
									</td>
								<?php endif; ?>

								<td class="center hidden-phone">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>

								<?php if (isset($this->items[0]->state)): ?>
									<td class="center">
										<?php echo JHtml::_('jgrid.published', $item->state, $i, 'subscribers.', $canChange, 'cb'); ?>
									</td>
								<?php endif; ?>

								<td>
									<?php if (isset($item->checked_out) && $item->checked_out) : ?>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'subscribers.', $canCheckin); ?>
									<?php endif; ?>

									<?php if ($canEdit) : ?>
										<a href="<?php echo JRoute::_('index.php?option=com_jmailalerts&task=subscriber.edit&id='.(int) $item->id); ?>">
											<?php echo $this->escape($item->name); echo'<br>'. $item->user_id; ?></a>
									<?php else : ?>
										<?php
										echo $this->escape($item->name);
										echo'<br>' . $item->user_id;
										?>
									<?php endif; ?>
								</td>

								<td>
									<?php
									echo $item->alert_name;
									echo'<br>' . $item->alert_id; ?>
								</td>

								<td>
									<?php echo JText::_($item->frequencyname); ?>
								</td>

								<td>
									<?php echo JHTML::_('date', $item->date, 'Y-m-d H:i:s'); ?>
								</td>

								<td>
					  			<?php
								if ($item->state)
								{
									?>
									&nbsp;
									<a id ="<?php echo (int) $item->id; ?>"
										rel="{handler: 'iframe', size: {x:700, y: 600}}"
										onclick="preview(<?php echo $item->user_id;?>,'<?php echo $item->date;?>',<?php echo $item->alert_id;?>,<?php echo $item->id;?>,'<?php echo $item->email_id;?>');"
										href="<?php echo JUri::root();?>"
										class="modal">
											<?php echo JText::_('COM_JMAILALERTS_PREVIEW');?>
									</a>
							  		<?php
								}
								?>
								</td>

								<?php if (isset($this->items[0]->id)): ?>
									<td class="center hidden-phone">
										<?php echo (int) $item->id; ?>
									</td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
