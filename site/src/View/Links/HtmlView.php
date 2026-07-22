<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Site
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\HwdLinks\Site\View\Links;

\defined('_JEXEC') or die;

use HWD\Component\HwdLinks\Site\Helper\LinksHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;



class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        // Assign data to the view
        $items = $this->get('Items');

        //TODO: Make menu item options to control this variables
        $start = 1; // Level to start. root is 1.
        $end   = 10; // End or max level.

        $this->items = LinksHelper::prepareTree($items, $start, $end);


        // Get the Web Asset Manager instance
        $doc = Factory::getApplication()->getDocument();
        $wa = $doc->getWebAssetManager();

        // Command Joomla to use your declared style asset
        $wa->useStyle('com_hwdlinks.frontend-css');

        // Display the view
        parent::display($tpl);
    }
}
