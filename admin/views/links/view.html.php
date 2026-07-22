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
 * HwdLinks View
 *
 * @since  0.0.1
 */
class HwdLinksViewLinks extends JViewLegacy
{
	/**
	 * Display the HwdLinks view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Get application
		$app = JFactory::getApplication();

		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state			= $this->get('State');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the toolbar and number of found items
		$this->addToolBar();

		// Prepare a mapping from parent id to the ids of its children
    $this->ordering = array();
    foreach ($this->items as $item)
    {
    	$this->ordering[$item->parent_id][] = $item->id;
    }

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();

	}


	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$title = JText::_('COM_HWDLINKS_MANAGER_HWDLINKS');

		if ($this->pagination->total)
		{
			$title .= " <span style='font-size: 0.8em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}

		JToolBarHelper::title($title, 'list-2');
		JToolbarHelper::addNew('link.add');
		JToolbarHelper::editList('link.edit');
		JToolbarHelper::deleteList('', 'links.delete');

		// Options button.
    if (JFactory::getUser()->authorise('core.admin', 'com_helloworld'))  {
			JToolBarHelper::preferences('com_hwdlinks');
    }

	}


	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_HWDLINKS_ADMINISTRATION'));
	}


}
