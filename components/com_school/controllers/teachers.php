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
 * HelloWorlds Controller
 *
 * @since  0.0.1
 */
use Joomla\Utilities\ArrayHelper;
class SchoolControllerTeachers extends JControllerAdmin
{
	/*
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Teacher', $prefix = 'SchoolModel', $config = array('ignore_request' => true))
	{
		
		$model = parent::getModel($name, $prefix, $config);
		//print_r($model);

		return $model;
	}

	public function delete()
	{
		// Check for request forgeries
		$this->checkToken();
		$app = JFactory::getApplication();

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');
		if (!is_array($cid) || count($cid) < 1)
		{
			\JLog::add(\JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), \JLog::WARNING, 'jerror');
		}
		else
		{
			if (!JFactory::getUser()->authorise( "teacher.delete", "com_school"))
			{
				$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->setHeader('status', 403, true);

				return;
			}
			// Get the model.
			$model = $this->getModel('teacher');	
			//print_r($model);
			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);
			// /$model->delete($cid);
			
			//Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(\JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{

				$this->setMessage($model->getError(), 'error');
			}

			//Invoke the postDelete method to allow for the child class to access the model.
			$this->postDeleteHook($model, $cid);
		}

		$this->setRedirect(\JRoute::_('http://ttpllt-php72.local/news_cms/index.php/component/school/?view=teachers', false));
	
	}
}