<?php


namespace HalloVerden\SecurityBundle\DependencyInjection;


use HalloVerden\Security\AbstractAuthenticator;
use HalloVerden\Security\AccessDefinitions\Constraints\HasAccessValidator;
use HalloVerden\Security\Interfaces\AccessDefinitionServiceInterface;
use HalloVerden\Security\Interfaces\AuthenticatorDeciderServiceInterface;
use HalloVerden\Security\Interfaces\SecurityInterface;
use HalloVerden\Security\Voters\BaseVoter;
use Metadata\Cache\FileCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class HalloVerdenSecurityExtension
 *
 * @package HalloVerden\SecurityBundle\DependencyInjection
 */
class HalloVerdenSecurityExtension extends Extension {

  /**
   * @inheritDoc
   * @throws \Exception
   */
  public function load(array $configs, ContainerBuilder $container) {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yaml');

    $authenticatorDeciderService = $container->getDefinition(AuthenticatorDeciderServiceInterface::class);
    $container->registerForAutoconfiguration(AbstractAuthenticator::class)->addMethodCall('setAuthenticatorDeciderService', [$authenticatorDeciderService]);

    $definition = $container->getDefinition(SecurityInterface::class);
    $definition->setArgument('$adminRoles', $config['admin_roles']);

    $container->registerForAutoconfiguration(BaseVoter::class)
      ->addTag('security.voter')
      ->addMethodCall('setAccessDefinitionService', [new Reference(AccessDefinitionServiceInterface::class)]);

    if (class_exists(ConstraintValidator::class)) {
      $hasAccessValidator = new Definition(HasAccessValidator::class, [new Reference(AccessDefinitionServiceInterface::class)]);
      $hasAccessValidator->addTag('validator.constraint_validator', ['alias' => HasAccessValidator::class]);
      $container->setDefinition(HasAccessValidator::class, $hasAccessValidator);
    }

    $container->getDefinition('hv.security_bundle.access_definitions.file_locator')
      ->replaceArgument('$dirs', $config['access_definitions']['dirs']);

    // Cache

    if ($config['access_definitions']['cache'] !== 'none') {
      $this->addCache($config, $container);
    }

  }

  /**
   * @param array            $config
   * @param ContainerBuilder $container
   */
  private function addCache(array $config, ContainerBuilder $container): void {
    $metadataCacheId = 'hv.security.access_definitions.metadata.cache';

    $container->getDefinition('hv.security.access_definitions.metadata_factory')
      ->addMethodCall('setCache', [new Reference($metadataCacheId)]);

    if ($config['access_definitions']['cache'] !== 'file') {
      $container->setAlias($metadataCacheId, new Alias($config['access_definitions']['cache']));
      return;
    }

    // File cache

    $dir = $container->getParameterBag()->resolveValue($config['access_definitions']['file_cache_dir']);
    if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
      throw new \RuntimeException(sprintf('Could not create cache directory "%s".', $dir));
    }

    $fileCache = new Definition(FileCache::class, [
      '$dir' => $config['access_definitions']['file_cache_dir']
    ]);

    $metadataFileCacheId = 'hv.security.access_definitions.metadata.file_cache';
    $container->setDefinition($metadataFileCacheId, $fileCache);
    $container->setAlias($metadataCacheId, new Alias($metadataFileCacheId));
  }

}
