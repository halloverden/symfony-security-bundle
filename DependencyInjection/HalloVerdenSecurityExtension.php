<?php


namespace HalloVerden\SecurityBundle\DependencyInjection;


use HalloVerden\Security\AbstractAuthenticator;
use HalloVerden\Security\AccessDefinitions\Constraints\HasAccessValidator;
use HalloVerden\Security\Interfaces\AccessDefinitionInterface;
use HalloVerden\Security\Interfaces\AuthenticatorDeciderServiceInterface;
use HalloVerden\Security\Interfaces\SecurityInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Validator\ConstraintValidator;

class HalloVerdenSecurityExtension extends Extension {

  /**
   * @inheritDoc
   * @throws \Exception
   */
  public function load(array $configs, ContainerBuilder $container) {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $container->registerForAutoconfiguration(AccessDefinitionInterface::class)->addTag('hallo_verden_security.access_definition');

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yaml');

    $authenticatorDeciderService = $container->getDefinition(AuthenticatorDeciderServiceInterface::class);
    $container->registerForAutoconfiguration(AbstractAuthenticator::class)->addMethodCall('setAuthenticatorDeciderService', [$authenticatorDeciderService]);

    $definition = $container->getDefinition(SecurityInterface::class);
    $definition->setArgument('$adminRoles', $config['admin_roles']);

    if (class_exists(ConstraintValidator::class)) {
      $hasAccessValidator = new Definition(HasAccessValidator::class, [new TaggedIteratorArgument('hallo_verden_security.access_definition')]);
      $hasAccessValidator->addTag('validator.constraint_validator', ['alias' => HasAccessValidator::class]);
      $container->setDefinition(HasAccessValidator::class, $hasAccessValidator);
    }
  }

}
