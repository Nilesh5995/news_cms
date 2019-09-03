<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<form action="<?php echo JRoute::_('index.php?option=com_school&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
    <input id="jform_title" type="hidden" name="helloworld-message-title"/>
    <div class="form-horizontal">
    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', 
        empty($this->item->id) ? JText::_('COM_SCHOOL_TAB_NEW_STUDENT') : JText::_('COM_SCHOOL_TAB_EDIT_MESSAGE')); ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_SCHOOL_DETAILS') ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <?php echo $this->form->renderFieldset('details');  ?>
                </div>
            </div>
        </fieldset>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'image', JText::_('COM_HELLOWORLD_TAB_IMAGE')); ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_SCHOOL_LEGEND_IMAGE') ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <?php echo $this->form->renderFieldset('image-info');  ?>
                </div>
            </div>
        </fieldset>
    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'personal', JText::_('COM_SCHOOL_TAB_RERSONAL')); ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_SCHOOL_LEGEND_PERSONAL') ?></legend>
            <div class="row-fluid">
                <div class="span12">
                    <?php echo $this->form->renderFieldset('personal');  ?>
                </div>
            </div>
        </fieldset>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
    <input type="hidden" name="task" value="studentedit.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>