HalloVerdenSecurityBundle
==============================

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require halloverden/symfony-security-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require alloverden/symfony-security-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    HalloVerden\SecurityBundle\HalloVerdenSecurityBundle::class => ['all' => true],
];
```

Authenticators
============

Authenticators can be used to allow authentication with an access token from your OpenID provider.

1. Create a class that implements `HalloVerden\Security\Interfaces\OauthUserProviderServiceInterface`
2. Enable authenticators and the class you want to use as services
    ```yaml
      HalloVerden\Security\Interfaces\OauthUserProviderServiceInterface:
        class: App\Services\OauthUserProviderService # Your class
    
      HalloVerden\Security\AccessTokenAuthenticator: ~
      HalloVerden\Security\ClientCredentialsAccessTokenAuthenticator: ~
    ```
3. Add authenticators to your security config.
    ```yaml
      guard:
        authenticators:
          - HalloVerden\Security\AccessTokenAuthenticator
        entry_point: HalloVerden\Security\AccessTokenAuthenticator
4. You also need services that implements `HalloVerden\Security\Interfaces\OauthTokenProviderServiceInterface` and
  `HalloVerden\Security\Interfaces\OauthJwkSetProviderServiceInterface` ( this can be skipped when using halloverden/symfony-oidc-client-bundle ) 

Access Definitions
============

Create a yaml file for each entity that needs to have a access definition. Example:

```yaml
App\Entity\Requests\TestRequest:
    canCreate:
        roles:
            - 'ROLE_ADMIN'
        scopes:
            - 'system.create:test-request'
    canRead:
        roles:
            - 'ROLE_ADMIN'
        scopes:
            - 'system.read:test-request'
    canUpdate:
        roles:
            - 'ROLE_ADMIN'
        scopes:
            - 'system.update:test-request'
    canDelete:
        roles:
            - 'ROLE_ADMIN'
        scopes:
            - 'system.delete:test-request'
    properties:
        test:
            canRead:
                roles:
                    - 'ROLE_USER'
                scopes:
                    - 'system.read:test-request.test'
            canWrite:
                roles:
                    - 'ROLE_USER'
                scopes:
                    - 'system.write:test-request.test'
        yoo:
            canWrite:
                roles:
                    - 'ROLE_USER'

```

Add the path for this access definition in the config file:
```yaml
hallo_verden_security:
  access_definitions:
    dirs:
      App\Entity\Requests: '%kernel.project_dir%/config/access_definitions/requests'
```

You can use `AccessDefinableExclusionStrategy` to skip properties the user does not have access too on serializing the deserializing.

There is also the `HasAccess` validator constraint that can check if user have access to specific property. 

In any other case you can use `AccessDefinitionService` to check access for specific class/property.
