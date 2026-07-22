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
 * HwdLinks Model
 *
 * @since  0.0.1
 */
class HwdLinksModelSections extends JModelList
{

	/**
	* Method to override getItems and use nested sets logic
	*
	* @return      array  An items array
	*/
	public function getItems() {

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hwdlinks/tables');
		$table = JTable::getInstance('Link', 'HwdLinksTable');

//TODO: Make menu item options to control this variables
		$baseNode 				= 1;

		// Getting items
		$items = $table->getTree($baseNode);

		// Preparing the items
		foreach($items as $key => $item):
			// Excluding unpublished links
			if($item->published == 0) {
				unset($items[$key]);
			}
			// Contructing links for articles
			if($item->type == 'article') {
				// Loading article
				$article = JTable::getInstance('content');
				$article->load($item->article);
				$url = JRoute::_(ContentHelperRoute::getArticleRoute($item->article, $article->catid, $article->language));
				$item->article = $url;
			}
			// Contructing links for menu items
			if($item->type == 'menu') {
				$app = JFactory::getApplication();
				$menu = $app->getMenu();
				$menuItem = $menu->getItem($item->menu);
				$url = JRoute::_($menuItem->link . '&Itemid=' . $menuItem->id);
				$item->menu = $url;
			}
		endforeach;

		return $items;

	}


	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	/*protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('*')
					->from($db->quoteName('#__hwdlinks_link'))
					->where($db->quoteName('published') . '= 1')
					->where($db->quoteName('id') . '> 1');

		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('title LIKE ' . $like);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(published IN (0, 1))');
		}

		// exclude root helloworld record
    $query->where('id > 1');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'lft');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}*/


}
