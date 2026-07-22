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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Date\Date;

/**
 * HwdLinks View
 *
 * @since  0.0.1
 */
class HwdLinksViewLink extends JViewLegacy
{
	/**
	 * View form
	 *
	 * @var         form
	 */
	protected $form = null;

	/**
	 * Display the Link view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		// Set modified to this date
		$timezone = JFactory::getUser()->getTimezone();
		$now = new Date('now');
		//$now->setTimezone($timezone);

		echo '<pre>';
		var_dump($timezone);
		var_dump($now->toSQL());
		echo $now->format(Text::_('DATE_FORMAT_FILTER_DATETIME'));
		echo '</pre>';
		$this->form->setValue('modified', null, date('Y-m-d H:i:s'));

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the toolbar
		$this->addToolBar();

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
		$input = JFactory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			$title = JText::_('COM_HWDLINKS_MANAGER_LINK_NEW');
		}
		else
		{
			$title = JText::_('COM_HWDLINKS_MANAGER_LINK_EDIT');
		}

		JToolbarHelper::title($title, 'link');
		JToolbarHelper::apply('link.apply');
		JToolbarHelper::save('link.save');
		JToolbarHelper::save2new('link.save2new');
		JToolbarHelper::save2copy('link.save2copy');
		JToolbarHelper::cancel(
			'link.cancel',
			$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		);
	}


	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = ($this->item->id < 1);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_HWDLINKS_LINK_CREATING') :
                JText::_('COM_HWDLINKS_LINK_EDITING'));
	}


}
