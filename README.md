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

Add in `config/services.yaml`.

```yaml
services:
    fos_rest.serializer.form_error_normalizer:
        class: Carve\ApiBundle\Serializer\Normalizer\FormErrorNormalizer
```

Add in `config/packages/framework.yaml`.

```yaml
framework:
    serializer:
        circular_reference_handler: carve_api.serializer.circular_reference_handler
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
