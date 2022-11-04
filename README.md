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

## Request execution error reporting

When action execution in backend encouters error that should passed to user (inform user about error). Throw `RequestExecutionException`.
The result will be returning 409 HTTP Code as:

```
{
    "code":409,
    "message":"error.requestExecutionFailed",
    "errors":[
        {"message":"functionality.error.somethingFailed","parameters":{"userName":"coolUser","areaNo":2}},
        {"message":"functionality.error.somethingElseFailed","parameters":{"deviceId":123}}
    ]
}
```

`error.requestExecutionFailed` - is default message value - it can be changed by setting 3rd parameter in `RequestExecutionException` constructor.
Constructor message (1st parameter) is added as first object in errors array, others can be added using addError method

Example below:

```
$exception = new RequestExecutionException('functionality.error.somethingFailed', ['userName' => 'coolUser', 'areaNo' => 2]);
$exception->addError('functionality.error.somethingElseFailed', ['deviceId' => 123]);
throw $exception;
```

Another example:

```
throw new RequestExecutionException('functionality.error.somethingFailed', ['userName' => 'coolUser', 'areaNo' => 2]);
```

By default forge frontend `ErrorDialog` by using `handleCatch` and `ErrorContext` will show response in dialog.
`message` (translated) will be used as dialog title, `errors` array will be shown as multiple `Alert`s with error serverity. Text will be translated using `message` as key and `parameters` as translation parameters.
`ErrorDialog` needs to be added to application layout


## Export (CSV and Excel)

When using `Carve\ApiBundle\EventListener\ViewResponseListener` and returning `Carve\ApiBundle\View\ExportCsvView` or `Carve\ApiBundle\View\ExportExcelView` from controller, the results will be automatically serialized and returned to as a `csv` or `xlsx` file.

Example usage:

```php
    use Carve\ApiBundle\View\ExportCsvView;
    use Carve\ApiBundle\Model\ExportQueryField;
    // ...
    public function customExportAction()
    {
        $results = $this->getRepository(Task::class)->findAll();
        $fields = [];

        // fields will most likely come from a POST request
        $field = new ExportQueryField();
        // What field should be included in the export
        $field->setField('name');
        // What label should be added for this field
        $field->setLabel('Name');
        $fields[] = $field;

        $filename = 'custom_export.csv';

        return new ExportCsvView($results, $fields, $filename);
    }
```

### Enums translation

By default every enum in export will be translated. The structure of translation string looks like this: `enum.entityName.fieldName.enumValue`. You can override the prefix by adding an `Carve\ApiBundle\Attribute\Export\ExportEnumPrefix` attribute.

In example below, translated string would be `enum.common.sourceType.enumValue`.

```php
    /**
     * Source type (upload or external url).
     */
    #[ExportEnumPrefix('enum.common.sourceType.')]
    #[ORM\Column(type: Types::STRING, enumType: SourceType::class)]
    private ?SourceType $sourceType = null;
```

### Export customization

You can customize common export cases by using similar pattern as `Carve\ApiBundle\Serializer\ExportEnumNormalizer`.

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
