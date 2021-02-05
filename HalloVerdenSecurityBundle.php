<?php


namespace HalloVerden\SecurityBundle;


use HalloVerden\SecurityBundle\DependencyInjection\Compiler\AddExpressionLanguageProvidersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class HalloVerdenSecurityBundle
 *
 * @package HalloVerden\SecurityBundle
 */
class HalloVerdenSecurityBundle extends Bundle {

  /**
   * @inheritDoc
   */
  public function build(ContainerBuilder $container) {
    parent::build($container);

    $container->addCompilerPass(new AddExpressionLanguageProvidersPass());
  }

}
