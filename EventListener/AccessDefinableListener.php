<?php


namespace HalloVerden\SecurityBundle\EventListener;


use HalloVerden\Security\Interfaces\AccessDefinableInterface;
use HalloVerden\Security\Interfaces\AccessDefinitionFilterServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AccessDefinableListener
 *
 * @package HalloVerden\SecurityBundle\EventListener
 */
class AccessDefinableListener implements EventSubscriberInterface {

  /**
   * @var AccessDefinitionFilterServiceInterface
   */
  private $accessDefinitionFilterService;

  /**
   * AccessDefinableListener constructor.
   *
   * @param AccessDefinitionFilterServiceInterface $accessDefinitionFilterService
   */
  public function __construct(AccessDefinitionFilterServiceInterface $accessDefinitionFilterService) {
    $this->accessDefinitionFilterService = $accessDefinitionFilterService;
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::CONTROLLER_ARGUMENTS => [
        ['onKernelControllerArguments', 1024]
      ]
    ];
  }

  /**
   * @param ControllerArgumentsEvent $event
   */
  public function onKernelControllerArguments(ControllerArgumentsEvent $event) {
    $request = $event->getRequest();

    foreach ($this->getAccessDefinableAttributes($request->attributes) as $accessDefinable) {
      $this->accessDefinitionFilterService->filterAccessDefinable($accessDefinable);
    }
  }

  /**
   * @param ParameterBag $attributes
   *
   * @return AccessDefinableInterface[]
   */
  private function getAccessDefinableAttributes(ParameterBag $attributes): array {
    $e = [];

    foreach ($attributes as $attribute) {
      if ($attribute instanceof AccessDefinableInterface) {
        $e[] = $attribute;
      }
    }

    return $e;
  }

}
