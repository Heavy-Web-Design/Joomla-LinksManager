<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

namespace HWD\Component\LinksManager\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;


class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = array())
    {
        $view = $this->getView('links', 'html');
        $model = $this->getModel('links');
        $view->setModel($model, true);
        $view->display();
    }
}
