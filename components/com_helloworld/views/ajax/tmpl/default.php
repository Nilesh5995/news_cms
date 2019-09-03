<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This layout file is for displaying the front end form for capturing a new helloworld message
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

?>

<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=ajax'); ?>"
    method="post" name="adminForm" id="adminForm" >
    <div class="form-horizontal">
		<fieldset class="adminform">
			<legend><?php // Cecho JText::_('COM_HELLOWORLD_LEGEND_DETAILS') ?></legend>
			<div class="row-fluid">
				<div class="span6">
				<?php
				if ($this->ajax)
				{
				?>
					<?php echo $this->ajax->renderFieldset('details');
				}
				?>


				</div>
			</div>
		</fieldset>
	</div>
<div id="searchmap">

    <button  type="button" class="btn btn-primary" onclick="searchHere();">
        <?php echo JText::_('Search') ?>
    </button>
    <div id="searchresults">
    </div>
</div>
	<div id="searchresults">
    </div>
</form>
<?php
if ($this->data_array)
{
?>
	<div class="container">
		<table class="table">
			<thead>
				<tr>
					<th width="10%">greeting</th>
					<th width="10%">Email</th>
					<th width="10%">mobile</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="10%"><?php print_r($this->data_array->greeting) ?></td>
					<td width="10%"><?php echo $this->data_array->email; ?></td>
					<td width="10%"><?php echo $this->data_array->mobile; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
<?php
}
