<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

\defined('_JEXEC') or die;

use HWD\Component\HwdLinks\Administrator\Extension\HwdLinksComponent;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory as ComponentDispatcherFactoryServiceProvider;
use Joomla\CMS\Extension\Service\Provider\MVCFactory as MVCFactoryServiceProvider;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;


return new class implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactoryServiceProvider('\\HWD\\Component\\HwdLinks'));
        $container->registerServiceProvider(new ComponentDispatcherFactoryServiceProvider('\\HWD\\Component\\HwdLinks'));
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new HwdLinksComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));

                return $component;
            }
        );
    }
};
