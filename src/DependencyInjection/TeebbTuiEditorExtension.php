<?php

namespace Teebb\TuiEditorBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class TeebbTuiEditorExtension extends Extension
{
    /**
     * @param array $configs   The configurations being loaded
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $resources = [
            'command',
            'installer',
            'config',
            'form',
            'renderer',
            'twig'
        ];

        foreach ($resources as $resource) {
            $loader->load($resource.'.xml');
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->getDefinition("teebb_tui_editor.configuration")
            ->setArgument(0, $config);

        $container->getDefinition("teebb_tui_editor.renderer")
                    ->setArgument(0, $config);

    }

}