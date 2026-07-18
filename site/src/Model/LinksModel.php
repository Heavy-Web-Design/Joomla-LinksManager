<?php
/**
 * @package     LinksManager.Site
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace HWD\Component\LinksManager\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentRouteHelper;

class LinksModel extends ListModel
{
    public function getItems($pk = null)
    {
        $table = $this->getTable('Link', 'Administrator');

        $baseNode = 1;

        // Getting items
        $items = $table->getTree($baseNode);

        // Preparing the items
        foreach ($items as $key => $item) :
            // Excluding unpublished links
            if ($item->published == 0) {
                unset($items[$key]);
                continue;
            }

            // Constructing links for articles
            if ($item->type === 'article') {
                $db = Factory::getDbo();
                $query = $db->getQuery(true)
                    ->select($db->quoteName(['catid', 'language']))
                    ->from($db->quoteName('#__content'))
                    ->where($db->quoteName('id') . ' = ' . (int) $item->article);

                $db->setQuery($query);
                $article = $db->loadObject();

                if ($article) {
                    $item->article = Route::_(ContentRouteHelper::getArticleRoute((int) $item->article, (int) $article->catid, $article->language));
                } else {
                    $item->article = '';
                }
            }

            // Constructing links for menu items
            if ($item->type === 'menu') {
                $app = Factory::getApplication();
                $menu = $app->getMenu();
                $menuItem = $menu->getItem($item->menu);

                if ($menuItem) {
                    $item->menu = Route::_($menuItem->link . '&Itemid=' . $menuItem->id);
                } else {
                    $item->menu = '';
                }
            }
        endforeach;

        return $items;
    }
}
