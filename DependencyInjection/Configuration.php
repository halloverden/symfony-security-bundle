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
      ->end()
    ;

    return $treeBuilder;
  }

}
