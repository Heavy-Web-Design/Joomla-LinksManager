<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

namespace HWD\Component\HwdLinks\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Table\Nested;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Access\Rules;

class LinkTable extends Nested
{
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__hwdlinks_link', 'id', $db);
    }

    /**
     * Overloaded bind function
     *
     * @param       array           named array
     * @return      null|string     null is operation was satisfactory, otherwise returns an error
     * @see JTable:bind
     * @since 1.5
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params'])) {
            // Convert the params field to a string.
            $parameter = new Registry();
            $parameter->loadArray($array['params']);
            $array['params'] = (string) $parameter;
        }

        // Bind the rules.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $rules = new Rules($array['rules']);
            $this->setRules($rules);
        }

        if (isset($array['parent_id'])) {
            if (!isset($array['id']) || $array['id'] == 0) {
                // new record
                $this->setLocation($array['parent_id'], 'last-child');
            } elseif (isset($array['linkordering'])) {
                // when saving a record load() is called before bind() so the table instance
                // will have properties which are the existing field values
                if ($this->parent_id == $array['parent_id']) {
                    // If first is chosen make the item the first child of the selected parent.
                    if ($array['linkordering'] == -1) {
                        $this->setLocation($array['parent_id'], 'first-child');
                    } elseif ($array['linkordering'] == -2) {
                        // If last is chosen make it the last child of the selected parent.
                        $this->setLocation($array['parent_id'], 'last-child');
                    } elseif ($array['linkordering'] && $this->id != $array['linkordering']) {
                        // Don't try to put an item after itself. All other ones put after the selected item.
                        $this->setLocation($array['linkordering'], 'after');
                    } elseif ($array['linkordering'] && $this->id == $array['linkordering']) {
                        // Just leave it where it is if no change is made.
                        unset($array['linkordering']);
                    }
                } else {
                    // Set the new parent id if parent id not matched and put in last position
                    $this->setLocation($array['parent_id'], 'last-child');
                }
            }
        }

        return parent::bind($array, $ignore);
    }


    /**
     * Method to compute the default name of the asset.
     * The default name is in the form `table_name.id`
     * where id is the value of the primary key of the table.
     *
     * @return string
     * @since 2.5
     */
    protected function getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_hwdlinks_link.link.' . (int) $this->$k;
    }



    /**
    * Method to return the title to use for the asset table.
    *
    * @return string
    * @since 2.5
    */
    protected function getAssetTitle()
    {
        return $this->title;
    }



    /**
     * Method to get the asset-parent-id of the item
     *
     * @return int
     */
    protected function getAssetParentId($table = null, $id = null)
    {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = \JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();

        // Find the parent-asset
        if (($this->catid) && !empty($this->catid)) {
            // The item has a category as asset-parent
            $assetParent->loadByName('com_hwdlinks.category.' . (int) $this->catid);
        } else {
            // The item has the component as asset-parent
            $assetParent->loadByName('com_hwdlinks');
        }

        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }
        return $assetParentId;
    }

    public function check()
    {
        $this->alias = trim($this->alias);
        if (empty($this->alias)) {
            $this->alias = $this->title;
        }
        $this->alias = OutputFilter::stringURLSafe($this->alias);
        return true;
    }

    public function delete($pk = null, $children = false)
    {
        return parent::delete($pk, $children);
    }
}
