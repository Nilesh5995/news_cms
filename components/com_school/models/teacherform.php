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
jimport( 'joomla.filesystem.path' );
jimport( 'joomla.application.component.model' );
/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */

class SchoolModelTeacherForm extends JModelForm
{
	public $path;
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type= 'teacherform', $prefix = 'SchoolTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);

	}
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_school.teacherform', 'teacherform', array(
				'control' => 'jform',
				'load_data' => $loadData
			)
			);
		if (empty($form))
		{
			$errors = $this->getErrors();
			throw new Exception(implode("\n", $errors), 500);
			//echo "error";
		}
		return $form;
	}
	public function getItem()
	{
		$app=JFactory::getApplication();
		$id=$app->input->get("id");
		$db = JFactory::getDbo();
		//print_r($db);
		$query = $db->getQuery(true);
		$query->select('*')
			 ->from('#__teacher')
			 ->where('id=' . (int)$id);
		$db->setQuery((string)$query);
		if ($this->item = $db->loadObject()) 
		{
			$image = new JRegistry;
			$image->loadString($this->item->image, 'JSON');
			$this->item->imageDetails = $image;
		

			return $this->item;
		}
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			'com_school.edit.studentform.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}


		return $data;
	}


	public function save($validateData, $fileinfo)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$table = $this->getTable();
		if ($table->save($validateData) === true)
		{
			$file = $fileinfo['imageinfo']['image'];
			if(!empty($file['name']))
			{
				if ($file['error'] == 4)   // no file uploaded (see PHP file upload error conditions)
					{
						$validData['imageinfo'] = null;
					}
					else
					{
						if ($file['error'] > 0)
						{
							 $app->enqueueMessage(JText::sprintf('COM_HELLOWORLD_ERROR_FILEUPLOAD', $file['error']), 'warning');

							return false;
						}

						// make sure filename is clean

						 $file['name'] = JFile::makeSafe($file['name']);
						if (!isset($file['name']))
						{
							// No filename (after the name was cleaned by JFile::makeSafe)
							$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_BADFILENAME'), 'warning');

							return false;
						}

						// files from Microsoft Windows can have spaces in the filenames
						$file['name'] = str_replace(' ', '-', $file['name']);

						// do checks against Media configuration parameters
						$mediaHelper = new JHelperMedia;
						if (!$mediaHelper->canUpload($file))
						{
							// The file can't be uploaded - the helper class will have enqueued the error message
							return false;
						}

						// prepare the uploaded file's destination pathnames
						$mediaparams = JComponentHelper::getParams('com_media');
						$relativePathname = JPath::clean($mediaparams->get($this->path,'images') . '/' . $file['name']);
						//print_r($relativePathname);
						
						$absolutePathname = JPATH_ROOT . '/' . $relativePathname;
						if (JFile::exists($absolutePathname))
						{
							// A file with this name already exists
							$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_FILE_EXISTS'), 'warning');
							return false;
						}

						// check file contents are clean, and copy it to destination pathname
						if (!JFile::upload($file['tmp_name'], $absolutePathname))
						{
							// Error in upload
							$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_UNABLE_TO_UPLOAD_FILE'));
							return false;
						}
						// Upload succeeded, so update the relative filename for storing in database
						$validData['imageinfo']['image'] = $relativePathname;
						$db = $table->getDBO();
						$query = $db->getQuery(true);	
						$db->query();
						if(!empty($validateData['id']))
						{
							$newID = $validateData['id'];
						}
						else
						{
							 $newID = $db->insertId();

						}
						$app = JFactory::getApplication();
						$fields = array(
						    $db->quoteName('image') . " = '".  $relativePathname."'"
						);
						// Conditions for which records should be updated.
						$conditions = array(
						    $db->quoteName('id') . ' = ' .$newID  
						);
					 	$query->update($db->quoteName('#__teacher'))->set($fields)->where($conditions);
					 	$db->setQuery($query);
					 	$result = $db->execute();
					 	$app->enqueueMessage("Record successsfully saved", 'warning');
						$app->redirect(JRoute::_('http://ttpllt-php72.local/news_cms/index.php?option=com_school&view=teachers'));					
					}
				}
			else
			{
				$app->enqueueMessage("Record successsfully saved", 'warning');
				$app->redirect(JRoute::_('http://ttpllt-php72.local/news_cms/index.php?option=com_school&view=teachers'));
			}
					

		}
		else
		{
			return false;
		}				
	}
}