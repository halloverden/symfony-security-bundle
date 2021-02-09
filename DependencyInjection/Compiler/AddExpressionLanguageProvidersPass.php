<?php


namespace HalloVerden\SecurityBundle\DependencyInjection\Compiler;


use HalloVerden\Security\AccessDefinitions\JMS\ExpressionLanguage\AccessDefinitionExpressionLanguageProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AddExpressionLanguageProvidersPass
 *
 * @package HalloVerden\SecurityBundle\DependencyInjection\Compiler
 */
class AddExpressionLanguageProvidersPass implements CompilerPassInterface {

  /**
   * @inheritDoc
   */
  public function process(ContainerBuilder $container) {
    $jmsExpressionLanguageDefinition = $container->findDefinition('jms_serializer.expression_language');
    $jmsExpressionLanguageDefinition->addMethodCall('registerProvider', [new Reference(AccessDefinitionExpressionLanguageProvider::class)]);
  }

}
