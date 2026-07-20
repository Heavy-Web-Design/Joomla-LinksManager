<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

namespace HWD\Component\HwdLinks\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Table\Nested;
use Joomla\Database\DatabaseInterface;

class LinkTable extends Nested
{
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__hwdlinks_link', 'id', $db);
    }
}
