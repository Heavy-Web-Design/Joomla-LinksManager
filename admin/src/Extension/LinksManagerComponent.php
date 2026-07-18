<?php
namespace HWD\Component\LinksManager\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\ComponentPHPBackend;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;

class LinksManagerComponent extends ComponentPHPBackend implements MVCFactoryAwareInterface
{
    use MVCFactoryAwareTrait;

    // This class can also implement RouterInterface or CategoryInterface
    // if your component handles custom routing or core Joomla categories
}
