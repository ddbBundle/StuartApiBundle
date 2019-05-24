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
            ->floatNode("vat_rate")->defaultValue(20.0)->end()
            ->arrayNode('authorized_webhook_ips')
            ->arrayPrototype()
                ->children()
                    ->arrayNode('sandbox')
                        ->defaultValue(["34.254.62.41", "54.194.139.211"])->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('production')
                        ->defaultValue(["108.128.110.19", "54.171.243.90", "52.51.60.65"])->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }


}
