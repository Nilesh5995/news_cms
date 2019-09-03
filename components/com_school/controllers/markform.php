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

class SchoolControllerMarkForm extends JControllerForm
{
	// public function saves()
 //    {
 //        $app=JFactory::getApplication();
	// 	echo $vJson=$app->input->get("id");
	// 	echo $vJson=$app->input->get("file");
 //    }
	public function cancel($key = null)
	{
		parent::cancel($key);
       
		// set up the redirect back to the same form
		$this->setRedirect(
			$url,
			JText::_(COM_SCHOOL_CANCELLED)
			);
	}
	/*
	 * Function handing the save for adding a new helloworld record
	 * Based on the save() function in the JControllerForm class
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app = JFactory::getApplication();
		$input = $app->input;
		$model = $this->getModel('markform');
		$currentUri = (string)JUri::getInstance();
		$input = JFactory::getApplication()->input;
		$data  = $input->get('jform', array(), 'array');
		$fileinfo = $input->files->get('jform', array(), 'array');
		$form = $model->getForm($data, false);
		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');
			return false;
		}
		// ... and then we validate the data against it
		// The validate function called below results in the running of the validate="..." routines
		// specified against the fields in the form xml file, and also filters the data 
		// according to the filter="..." specified in the same place (removing html tags by default in strings)
		$validData = $model->validate($form, $data);
		// Handle the case where there are validation errors
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $this->getErrors();

			// Display up to three validation messages to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
					//$app->redirect(JRoute::_('index.php'));
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
					//$app->redirect(JRoute::_('https://localhost/news_cms/index.php/component/school/?view=studentform&layout=edit'));
				}
			}
			
		}
		else
		{
			$data = $model->save($validData,$fileinfo);

		}

		
    }

}
