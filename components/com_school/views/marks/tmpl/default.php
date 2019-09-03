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
<form action="index.php?option=com_school&view=students" method="post" id="adminForm" name="adminForm">
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
			<th width="10%">
				<?php echo JText::_('Teacher Id') ;?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_ID') ;?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_MARATHI'); ?>
			</th>
			<!-- <th width="30%">
                    <?php //echo JText::_('COM_SCHOOL_SCHOOLS_IMAGE'); ?>
            </th> -->
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_HINDI'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_ENGLISH'); ?>
			</th>
			<th width="10%">
                    <?php echo JText::_('COM_STUDENT_MATH'); ?>
            </th> 
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_SCIENCE'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_STUDENT_PERCENTAGE'); ?>
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
					$link = JRoute::_('index.php?option=com_school&view=mark&id=' . $row->id);
					$link1 = JRoute::_('index.php?option=com_school&view=studentform&layout=edit&id=' . $row->id);
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
						<!-- <td>
							<?php //echo JHtml::_('grid.id', $i, $row->id); ?>
						</td> -->
						<td>
								<a href="<?php echo $link1; ?>" title="<?php echo JText::_('EDIT'); ?>">
										<?php echo $row->tid; ?>
								</a>
						</td>
						<td>
								<a href="<?php echo $link1; ?>" title="<?php echo JText::_('EDIT'); ?>">
										<?php echo $row->sid; ?>
								</a>
						</td>
						<td align="center">
							<?php echo $row->marathi; ?>
						</td>
						<td align="center">
							<?php echo $row->hindi; ?>
						</td>
						<td align="center">
							<?php echo $row->english; ?>				
						</td>
						<td align="center">
							<?php echo $row->math; ?>
						</td>
						<td align="center">
							<?php echo $row->science; ?>				
						</td>
					
						<td align="center">
							<?php echo $row->percentage; ?>
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