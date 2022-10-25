# Carve API

Consistent and reusable way of composing REST API endpoints for Symfony.

IMPORTANT! Work in progress.

Offers consistent and reusable way of composing REST API endpoints
Allows single-minded endpoint customization
Automaticly generates OpenAPI documentation
Introduces deny functionality to allow easier access control that includes feedback messages
Adds layer of constraints that have REST API friendly messages

Build with:

-   FOSRestBundle
-   Symfony serializer
-   OpenAPI

## Configuration

Add in `config/packages/doctrine.yaml`. It will enable storing `Types::DATETIME_MUTABLE` always in UTC timezone.

```yaml
doctrine:
    dbal:
        types:
            datetime: Carve\ApiBundle\DBAL\Types\UTCDateTimeType
```

Add in `config/services.yaml`. It will override default FormErrorNormalizer to additionally pass parameters from error messages.

```yaml
services:
    fos_rest.serializer.form_error_normalizer:
        class: Carve\ApiBundle\Serializer\Normalizer\FormErrorNormalizer
```

Add in `config/services.yaml`. It will override default ViewResponseListener to additionally handle exporting views.

```yaml
services:
    fos_rest.view_response_listener:
        class: Carve\ApiBundle\EventListener\ViewResponseListener
```

Add in `config/packages/framework.yaml`. It will add default circular reference handling.

```yaml
framework:
    serializer:
        circular_reference_handler: carve_api.serializer.circular_reference_handler
```

Modify `src/Kernel.php` to override `FormModelDescriber` class.

```php
<?php

namespace App;

use Carve\ApiBundle\ModelDescriber\FormModelDescriber;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function process(ContainerBuilder $container): void
    {
        $formModelDescriberService = $container->getDefinition('nelmio_api_doc.model_describers.form');
        $formModelDescriberService->setClass(FormModelDescriber::class);
    }
}

```

## Local development

Add to `composer.json` in your project following lines:

```
    "repositories": [
        {
            "type": "path",
            "url": "/var/www/carve-api"
        }
    ],
```

Change `"/var/www/carve-api"` to your local path to the package. It should point to the root directory of `carve-api` (this means `composer.json` of `carve-api` is located in `/var/www/carve-api/composer.json`).

Afterwads execute:

```
composer require "awtyklo/carve-api @dev"
```

It should link local package instead of one from remote.

**Note!** It will change `composer.json`. Please remember that while committing changes.

TODO: How to revert this
