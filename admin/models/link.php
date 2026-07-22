<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Link Model
 *
 * @since  0.0.1
 */
class HwdLinksModelLink extends JModelAdmin
{
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
	public function getTable($type = 'Link', $prefix = 'HwdLinksTable', $config = array())
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
		// Get the form.
		$form = $this->loadForm(
			'com_hwdlinks.link',
			'link',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
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
			'com_hwdlinks.edit.link.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	/**
	 * Method to override the JModelAdmin save() function to handle Save as Copy correctly
	 *
	 * @param   The hwdlinks record data submitted from the form.
	 *
	 * @return  parent::save() return value
	 */
	public function save($data)
	{

		//$input = JFactory::getApplication()->input;

		//JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');

		// Validate the category id
		// validateCategoryId() returns 0 if the catid can't be found
		/*if ((int) $data['catid'] > 0)
		{
			$data['catid'] = CategoriesHelper::validateCategoryId($data['catid'], 'com_helloworld');
		}*/

		// Alter the title and alias for save as copy
		/*if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['title'] == $origTable->greeting)
			{
				list($greeting, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title'] = $greeting;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}
			// standard Joomla practice is to set the new record as unpublished
			$data['published'] = 0;
		}*/

		$result = parent::save($data);

		if ($result)
		{
			$this->getTable()->rebuild(1);
		}

		return $result;

	}


	/**
	 * Prepare a link record for saving in the database
	 */
	protected function prepareTable($table)
	{
		//Getting user info
		$user = JFactory::getUser();

		// If is new item
		$isNew = ($table->id == 0);

		if ($isNew)
		{
			if(empty($table->created)) {
				$table->created 	 = date('Y-m-d H:i:s');
			}
			$table->created_by = $user->id;
		}
		/*else
		{
			if(empty($table->modified)) {
				$table->modified 	  = date('Y-m-d H:i:s');
			}
			$table->modified_by = $user->id;
		}*/
		// Set ordering to the last item if not set
		/*if (empty($table->ordering))
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->select('MAX(ordering)')
				->from('#__hwdlinks_link');

			$db->setQuery($query);
			$max = $db->loadResult();

			$table->ordering = $max + 1;
		}*/
	}


	/**
	 * Save the record reordering after a record is dragged to a new position in the links view
	 */
	public function saveorder($idArray = null, $lft_array = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}



}
