<?php
/**
 * @package     Joomla.Administrator
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
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 * @since       0.0.9
 */
use Joomla\Utilities\ArrayHelper;
class SchoolControllerTeacherForm extends JControllerForm
{
	// public function cancel($key = null)
	// {
	// 	parent::cancel($key);
       
	// 	// set up the redirect back to the same form
	// 	$this->setRedirect(
	// 		$url,
	// 		JText::_('COM_SCHOOL_CANCELLED')
	// 		);
	// }
	public function cancel($key = null)
    {
        parent::cancel($key);
        
        // set up the redirect back to the same form
        $this->setRedirect(
            (string)JUri::getInstance(), 
            JText::_('COM_HELLOWORLD_ADD_CANCELLED')
		);
    }
	public function save($key = null, $urlVar = null)
	{

		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$app = JFactory::getApplication();
		$input = $app->input;
		$model = $this->getModel('teacherform');
		$currentUri = (string)JUri::getInstance();
		if (!JFactory::getUser()->authorise( "teacher.create", "com_school"))
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);

			return;
		}
		$input = JFactory::getApplication()->input;
		$data  = $input->get('jform', array(), 'array');
		$form = $model->getForm($data, false);
		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');
			return false;
		}
		$validData = $model->validate($form, $data);
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

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
			$fileinfo = $input->files->get('jform', array(), 'array');
			$data = $model->save($validData,$fileinfo);
		}

    }

}