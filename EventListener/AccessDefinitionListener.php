<?php


namespace HalloVerden\SecurityBundle\EventListener;


use HalloVerden\Security\Interfaces\AccessDefinableInterface;
use HalloVerden\Security\Interfaces\AccessDefinitionServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AccessDefinitionListener implements EventSubscriberInterface {

  /**
   * @var AccessDefinitionServiceInterface
   */
  private $accessDefinitionService;

  /**
   * AccessDefinitionSubscriber constructor.
   *
   * @param AccessDefinitionServiceInterface $accessDefinitionService
   */
  public function __construct(AccessDefinitionServiceInterface $accessDefinitionService) {
    $this->accessDefinitionService = $accessDefinitionService;
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

    $accessDefinableAttributes = $this->getAccessDefinableAttributes($request->attributes);

    foreach ($accessDefinableAttributes as $accessDefinable) {
      $this->accessDefinitionService->handleAccessDefinable($accessDefinable);
    }
  }

  /**
   * @param ParameterBag $attributes
   * @return array
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
