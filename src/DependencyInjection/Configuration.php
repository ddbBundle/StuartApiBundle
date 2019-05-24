<?php

namespace DdB\StuartApiBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("stuart_api");

        $rootNode
            ->children()
                ->scalarNode("private_key")->defaultNull()->end()
                ->scalarNode("public_key")->defaultNull()->end()
                ->enumNode("environment")->values(["SANDBOX", "PRODUCTION"])->defaultValue("SANDBOX")->end()
                ->floatNode("vat_rate")->defaultValue(20.0)
            ->end()
            ->arrayNode('authorized_webhook_ips')
            ->children()
            ->arrayNode('sandbox')
            ->scalarPrototype()->defaultValue(["34.254.62.41", "54.194.139.211"])->end()
            ->end()
            ->arrayNode('production')
            ->scalarPrototype()->end()
            ->end()
            ->end();

        return $treeBuilder;
    }


}
