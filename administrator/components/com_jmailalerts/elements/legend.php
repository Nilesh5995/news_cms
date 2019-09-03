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


jimport('joomla.form.formfield');

/**
 * Help by @manoj
 * How to use this?
 * See the code below that needs to be added in form xml
 * Make sure, you pass a unique id for each field
 * Also pass a hint field as Help text
 *
 * <field menu="hide" type="legend" id="jma-product-display" 
 * name="jma-product-display" 
 * default="COM_QUICK2CART_DISPLAY_SETTINGS" 
 * hint="COM_QUICK2CART_DISPLAY_SETTINGS_HINT" label="" />
 *
 */

/**
 * Custom Legend field for component params.
 *
 * @since  2.5.0
 */
class JFormFieldLegend extends JFormField
{
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * 
	 * @since	1.5.0
	 */
	public function getInput()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_jmailalerts/assets/css/jmailalerts.css');

		$legendClass = 'jma-elements-legend';
		$hintClass = "jma-elements-legend-hint";

		$hint = $this->hint;

		// Tada... Let's remove controls class from parent
		// And, remove control-group class from grandparent
		$script = 'jQuery(document).ready(function(){
			jQuery("#' . $this->id . '").parent().removeClass("controls");
			jQuery("#' . $this->id . '").parent().parent().removeClass("control-group");
		});';

		$document->addScriptDeclaration($script);

		// Show them a legend.
		$return = '<legend class="clearfix pull-left ' . $legendClass . '" id="' . $this->id . '">' . JText::_($this->value) . '</legend>';

		// Show them a hint below the legend.
		// Let them go - GaGa about the legend.
		if (!empty($hint))
		{
			$return .= '<span class="disabled ' . $hintClass . '">' . JText::_($hint) . '</span>';
			$return .= '<br/><br/>';
		}

		return $return;
	}
}
