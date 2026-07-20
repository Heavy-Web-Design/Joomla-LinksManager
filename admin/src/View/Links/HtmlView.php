<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\HwdLinks\Administrator\View\Links;

\defined('_JEXEC') or die;

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
    protected $items;
    protected $pagination;
    protected $state;
    public $filterForm;
    public $activeFilters;

    public function display($tpl = null)
    {
        // Get data from the model
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        $errors = $this->get('Errors');
        if (!empty($errors)) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // Set the toolbar and number of found items
        $this->addToolbar();

        // Prepare a mapping from parent id to the ids of its children
        $this->ordering = [];
        foreach ($this->items as $item) {
            $this->ordering[$item->parent_id][] = $item->id;
        }

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument($this->document);
    }

    protected function addToolbar()
    {
        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        $title = Text::_('COM_HWDLINKS_MANAGER_HWDLINKS');

        if ($this->pagination->total) {
            $title .= " <span style='font-size: 0.8em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
        }

        ToolbarHelper::title($title, 'list-2');
        ToolbarHelper::addNew('link.add');
        ToolbarHelper::editList('link.edit');
        ToolbarHelper::deleteList('', 'links.delete');

        $canDo = ContentHelper::getActions('com_hwdlinks');

        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_hwdlinks');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    public function setDocument(Document $document): void
    {
        // Call the parent method to ensure the document is properly assigned
        parent::setDocument($document);
        $document->setTitle(Text::_('COM_HWDLINKS_ADMINISTRATION'));
    }
}
