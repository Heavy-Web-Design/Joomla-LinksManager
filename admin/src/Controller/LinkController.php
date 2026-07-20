<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     LinksManager.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\LinksManager\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

class LinkController extends FormController
{
    // Keep form actions tied to the hwdlinks component so the New button redirects correctly.
    // Fixes issue where the list buttons redirects to com_linksmanager instead of com_hwdlinks.
    protected $option = 'com_hwdlinks';
    protected $view_item = 'link';
    protected $view_list = 'links';

    public function display($cachable = false, $urlparams = array())
    {
        return parent::display($cachable, $urlparams);
    }

    public function add($key = null, $urlVar = null)
    {
        // Ensure the add action uses the same component/view mapping as the list screen.
        $this->option = 'com_hwdlinks';
        $this->view_item = 'link';
        $this->view_list = 'links';

        return parent::add($key, $urlVar);
    }
}
