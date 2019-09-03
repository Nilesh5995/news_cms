<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Controller
 *
 * @package     Joomla.Site
 * @subpackage  com_helloworld
 *
 * Used to handle the http POST from the front-end form which allows 
 * users to enter a new helloworld message
 *
 */
class HelloWorldControllerAjax extends JControllerForm
{
	public function getData()
    {
        $app=JFactory::getApplication();
		$vJson=$app->input->get("id");
        $model = $this->getModel('ajaxdata');
		$data=$model->getData($vJson);
		if ($data) 
		{
		
			echo "<ul>";
			echo "<li>";
			echo $data->greeting;
			echo "</li>";
			echo "<li>";
			echo $data->email;
			echo "</li>";
			echo "<li>";
			echo $data->mobile;
			echo "</li>";
			echo "</ul>";
			$view = $this->getView('ajax','html');
			$view->data_array = $data;
			$view->display();
		}
		else
		{
			echo "<h3>data not available.</h3>";
		}	
		die();
        if (!empty($vJson=$app->input->getCmd("id"))) 
        {				
        }
        else 
        {
        }
      
    }
}