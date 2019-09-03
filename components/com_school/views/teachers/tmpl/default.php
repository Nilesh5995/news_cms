<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
use Joomla\Registry\Registry;

?>
<form action="index.php?option=com_school&view=teachers" method="post" id="adminForm" name="adminForm">
			<button type="button" class="btn btn-denger" onclick="Joomla.submitbutton('teachers.delete')">
			<span class="icon-delete"></span><?php echo JText::_('JDELETE') ?>
			</button>
			<?php  $links = JRoute::_('index.php?option=com_school&view=teacherform')?>
			<a href="<?php echo $links;?>">
				<button type="button" class="btn btn-denger">	
				<span class="icon-new"></span><?php echo JText::_('ADD') ?>
				</button>
			</a>
			<button type="button" class="btn btn-denger" onclick="Joomla.submitbutton('teacherform.edit')">
			<span class="icon-edit"></span><?php echo JText::_('EDIT') ?>
			</button>
								
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th width="2%">
				<?php echo JText::_('#');
				 ?>
			</th>
			<th width="2%">
				<?php echo JText::_('Show');
				 ?>
			</th>
			<th width="2%">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_NAME') ;?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_CLASS'); ?>
			</th>
			<!-- <th width="30%">
                    <?php //echo JText::_('COM_SCHOOL_SCHOOLS_IMAGE'); ?>
            </th> -->
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_ADDRESS'); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('COM_STUDENT_ID'); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $row) : 
					$link = JRoute::_('index.php?option=com_school&view=teacher&layout=show&id=' . $row->id);
					$link1 = JRoute::_('index.php?option=com_school&view=teacherform&id=' . $row->id);
					// echo $row->image;
					 //$row->image = new Registry;
					 //$row->image->loadString($row->imageInfo);
                    
				?>

					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('EDIT'); ?>">Show</a>
						</td>
						
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td>
								<a href="<?php echo $link1; ?>" title="<?php echo JText::_('EDIT'); ?>">
										<?php echo $row->fname.' '.$row->mname.' '.$row->lname; ?>
								</a>
						</td>
						<td align="center">
							<?php echo $row->education; ?>
						</td>
						<td align="center">
							<?php echo $row->address.' '.$row->city.' '.$row->state.' '.$row->pincode; ?>				
						</td>
					
						<td align="center">
							<?php echo $row->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>