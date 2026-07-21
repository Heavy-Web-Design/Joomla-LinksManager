<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\HwdLinks\Administrator\View\Link;

\defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;



class HtmlView extends BaseHtmlView
{
    /**
     * The \JForm object
     *
     * @var  \JForm
     */
    protected $form;

    /**
     * The active item
     *
     * @var  object
     */
    protected $item;

    /**
     * The model state
     *
     * @var  object
     */
    protected $state;

    /**
     * The actions the user is authorised to perform
     *
     * @var  \JObject
     */
    protected $canDo;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     *
     * @throws \Exception
     * @since   1.6
     */
    public function display($tpl = null)
    {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');

        // Set modified to this date
        //$timezone = Factory::getUser()->getTimezone();
        //$now = new Date('now');

        $this->form->setValue('modified', null, date('Y-m-d H:i:s'));

        // Check for errors.
        $errors = $this->get('Errors');
        if (!empty($errors)) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // Set the toolbar and number of found items
        $this->addToolbar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument($this->document);
    }

    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);

        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(
            Text::_('COM_HWDLINKS_MANAGER_LINK_' . ($isNew ? 'NEW' : 'EDIT')),
            'list-2'
        );

        ToolbarHelper::apply('link.apply');
        ToolbarHelper::save('link.save');
        ToolbarHelper::save2new('link.save2new');
        ToolbarHelper::save2copy('link.save2copy');
        ToolbarHelper::cancel('link.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    public function setDocument(Document $document): void
    {
        $itemId = isset($this->item->id) ? (int) $this->item->id : 0;
        $isNew = ($itemId === 0);
        // Call the parent method to ensure the document is properly assigned
        parent::setDocument($document);
        $document->setTitle($isNew ? Text::_('COM_HWDLINKS_LINK_CREATING') : Text::_('COM_HWDLINKS_LINK_EDITING'));
    }
}
