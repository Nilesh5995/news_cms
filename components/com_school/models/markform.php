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
/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */

class  SchoolModelMarkform extends JModelForm
{
	public $form;
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
	public function getTable($type= 'Markform', $prefix = 'SchoolTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);

	}

	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(
			'com_school.markform',
			'markform',
			array(
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
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			'com_school.edit.markform.data',
			array()
		);
		return $data;

	}
	public function FileRead($file)
	{
		
			$marksArray = array();
			$myfile = fopen($file, "r") or die("Unable to open file!");
			$marksArray = fread($myfile,filesize($file));
			fclose($myfile);
			return $marksArray ;

	}
	public function UpdateMarks($fileread,$newID)
	{
		//$len = strlen($fileread);

		$i = 0;
		$fileread = (explode(",",$fileread));
		//print_r($fileread);
		$count = count($fileread);
		while($count > $i)
		{

			if(empty($fileread[$i]))
			{
				$fileread[$i]=0;
			}
			if(empty($fileread[$i+1]))
			{
				$fileread[$i+1]= 0;
			}if(empty($fileread[$i+2]))
			{
				$fileread[$i+2]= 0;
			}if(empty($fileread[$i+3]))
			{
				$fileread[$i+3]= 0;
			}if(empty($fileread[$i+4]))
			{
				$fileread[$i+4]= 0;
			}if(empty($fileread[$i+5]))
			{
				$fileread[$i+5] = 0;
			}
			if(empty($fileread[$i+6]))
			{
				$fileread[$i+6] = 0;
			}
			$percentage = ($fileread[$i+2] + $fileread[$i+3] + $fileread[$i+4] + $fileread[$i+5] + $fileread[$i+6])/5;			
			if(empty($percentage))
			{
				$percentage  = 0;
			}
			$marks = array("tid"=>$fileread[$i], "sid"=>$fileread[$i+1],"marathi"=>$fileread[$i+2], "hindi"=>$fileread[$i+3],   
                  "english"=>$fileread[$i+4], "science"=>$fileread[$i+5],   
                  "math"=>$fileread[$i+6],"percentage"=>$percentage );	
				 $table = $this->getTable();
				if ($table->save($marks) === true)
				{
					echo "data inserted";
				}
				$i = $i+7;
		}
	}
	public function save($validateData, $fileinfo)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$table = $this->getTable();
		//print_r($validateData);

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
						if (!JFile::upload($file['tmp_name'], $absolutePathname))
						{
							// Error in upload
							$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_UNABLE_TO_UPLOAD_FILE'));
							return false;
						}
						// Upload succeeded, so update the relative filename for storing in database
						$validData['imageinfo']['image'] = $relativePathname;
						$fileread = $this->FileRead($relativePathname);
						$db = $table->getDBO();
						$newID = $db->insertId();
						$query = $db->getQuery(true);	
						$db->query();
						$app = JFactory::getApplication();
						$fields = array(
						    $db->quoteName('file') . " = '".  $relativePathname."'"
						);
						// Conditions for which records should be updated.
						$conditions = array(
						    $db->quoteName('id') . ' = ' .$newID  
						);
					 	$query->update($db->quoteName('#__mark'))->set($fields)->where($conditions);
					 	$db->setQuery($query);
					 	$result = $db->execute();
					 	$UpdateMarks=$this->UpdateMarks($fileread,$newID);
					 	$app->enqueueMessage("Record successsfully saved", 'warning');
						$app->redirect(JRoute::_('http://ttpllt-php72.local/news_cms/index.php?option=com_school&view=marks'));					
					}
				}
			else
			{
				$app->enqueueMessage("Record successsfully saved", 'warning');
				$app->redirect(JRoute::_('http://ttpllt-php72.local/news_cms/index.php?option=com_school&view=marks'));
			}
					

		}
		else
		{
			return false;
		}				
	}

}