<?php

namespace DdB\StuartApiBundle\DependencyInjection;

use Stuart\Client;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class StuartApiExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition("ddb_stuart_api.stuart_api");
        $definition->setArgument(0, $config['private_key']);
        $definition->setArgument(1, $config['public_key']);
        $definition->setArgument(2, $config['environment']);
        $definition->setArgument(3, $config['vat_rate']);
        $definition->setArgument(4, $config['authorized_webhook_ips']);
    }
}
