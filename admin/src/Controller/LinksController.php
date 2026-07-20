<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\HwdLinks\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class LinksController extends AdminController
{
    /**
     * Proxy method to get the singular model
     */
    public function getModel($name = 'Link', $prefix = 'Administrator', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
