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

use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
    /**
     * The default view for the gateway entry point.
     *
     * @var    string
     */
    protected $default_view = 'links';

    public function display($cachable = false, $urlparams = array())
    {
        return parent::display($cachable, $urlparams);
    }
}
