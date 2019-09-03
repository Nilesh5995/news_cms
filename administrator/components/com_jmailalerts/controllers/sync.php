<?php
/**
 * @package		com_jmailalerts
 * @version		$versionID$
 * @author		TechJoomla
 * @author mail	extensions@techjoomla.com
 * @website		http://techjoomla.com
 * @copyright	Copyright © 2009-2013 TechJoomla. All rights reserved.
 * @license		GNU General Public License version 2, or later
*/
defined('_JEXEC') or die('Restricted access');
require_once( JPATH_COMPONENT.DS.'views'.DS.'sync'.DS.'view.html.php' );
jimport('joomla.application.component.controller');

class jmailalertsControllerSync extends jmailalertsController
{
    /*
     * 
     * 
     */
    function save()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cache=&JFactory::getCache('com_jmailalerts');
        $cache->clean();
        $post=JRequest::get('post');
        $model=&$this->getModel('sync');

        if ($model->store()){
            $msg = JText::_('MENU_ITEM_SAVED' );
        } else {
            $msg = JText::_('ERROR_SAVING_MENU_ITEM');
        }
    }

}
?>
