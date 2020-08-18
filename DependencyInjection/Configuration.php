<?php


namespace HalloVerden\SecurityBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

  /**
   * @inheritDoc
   */
  public function getConfigTreeBuilder() {
    $treeBuilder = new TreeBuilder('hallo_verden_security');

    $treeBuilder->getRootNode()
      ->addDefaultsIfNotSet()
      ->children()
        ->arrayNode('admin_roles')
          ->defaultValue([])
          ->scalarPrototype()->end()
        ->end()
        ->arrayNode('access_definitions')
          ->addDefaultsIfNotSet()
          ->children()
            ->arrayNode('dirs')
              ->defaultValue([])
              ->scalarPrototype()->end()
            ->end()
            ->scalarNode('cache')->defaultValue('file')->end()
            ->scalarNode('file_cache_dir')->defaultValue('%kernel.cache_dir%/hv_security_access_definitions')->end()
          ->end()
        ->end()
      ->end()
    ;

    return $treeBuilder;
  }

}
