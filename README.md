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

Designed to provide additional information in the response when action in a controller could be executed only partially. Throwing `RequestExecutionException` will result in `409` HTTP code.

Below you can find more information about default HTTP codes and how `RequestExecutionException` with `409` fits in it.

-   `200` - Request has been executed successfully. Additional data can be included in the response (i.e. updated object).
-   `204` - Request has been executed successfully. No additional is included in the response.
-   `400` - Request could not be executed due to invalid payload (widely used with forms). Form errors are serialized by `Carve\ApiBundle\Serializer\Normalizer\FormErrorNormalizer`.
-   `409` - Request has been executed only partially (payload is correct). Additional information is included in the response.
-   `500` - Unexpected error.

### Response structure

Example structure as follows (TypeScript).

```ts
type RequestExecutionSeverity = "warning" | "error";

// eslint-disable-next-line
type RequestExecutionExceptionPayload = any;

interface TranslateVariablesInterface {
    [index: string]: any;
}

interface RequestExecutionExceptionErrorType {
    message: string;
    parameters?: TranslateVariablesInterface;
    severity: RequestExecutionSeverity;
}

interface RequestExecutionExceptionType {
    code: number;
    payload: RequestExecutionExceptionPayload;
    severity: RequestExecutionSeverity;
    errors: RequestExecutionExceptionErrorType[];
}
```

First level `severity` will take the value of the highest `severity` from the messages.

```json
{
    "code": 409,
    "severity": "error",
    "payload": null,
    "errors": [
        {
            "message": "functionality.error.processingWarning",
            "parameters": { "userName": "coolUser", "areaNo": 2 },
            "severity": "warning"
        },
        {
            "message": "functionality.error.somethingFailed",
            "parameters": { "deviceId": 123 },
            "severity": "error"
        }
    ]
}
```

### Severity interpretation

`error` means that at some point in action execution there was an error that prevented executing remaining steps. Good example would be not be able to connect to third party system (i.e. Google services).

`warning` means that at some point in action execution there was an issue that should not happen, but it has been managed and remaining steps has been executed. Good example would be not be removing action of a resource from third party system which resulted in lack of such resource (our application expects that resource exists and tries to remove it, but the resource does not exist in third party system).

## Usage examples

TODO Fix this (right now it has old examples - some of the are correct. Extend with mergeAsX function example)

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

### Integration with forge

By default forge frontend `ErrorDialog` by using `handleCatch` and `ErrorContext` will show response in dialog.
`message` (translated) will be used as dialog title, `errors` array will be shown as multiple `Alert`s with error serverity. Text will be translated using `message` as key and `parameters` as translation parameters.
`ErrorDialog` needs to be added to application layout

## Batch processing

Batch processing is designed to process results that are possible to query via list endpoint.

```php
    #[Rest\Post('/batch/disable')]
    // TODO Rest API docs
    public function batchDisableAction(Request $request)
    {
        $process = function (Device $device) {
            $device->setEnabled(false);
        };

        return $this->handleBatchForm($process, $request);
    }
```

You can customize returned by returning custom `BatchResult` in `$process` function. When nothing is returned a `BatchResult` with `SUCCESS` status will be returned (controller by `getDefaultBatchProcessEmptyResult` function).

```php
    $this->handleBatchForm($process, $request, DeviceDeny::DISABLE);
```

```php
use Carve\ApiBundle\Model\BatchResult;
use Carve\ApiBundle\Enum\BatchResultStatus;

    $process = function (Device $device) {
        $device->setEnabled(false);

        // Your logic
        if (true) {
            return new BatchResult($device, BatchResultStatus::SKIPPED, 'batch.device.variableDelete.skipped.missing');
        }
    };
```

You can also use `denyKey` to skip any results that should not be processed (`BatchResult` with `SKIPPED` and message based on `denyKey` will be returned).

You can use following pattern to define additional field in `BatchQueryType` form (which has only `sorting` and `ids` fields).

Define form that includes any needed fields and extends `BatchQueryType`. Fields should not be mapped or you will need to update the data model of form (which is also a good solution).

```php
<?php

declare(strict_types=1);

namespace App\Form;

use Carve\ApiBundle\Form\BatchQueryType;
use Carve\ApiBundle\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;

class BatchVariableDeleteType extends BatchQueryType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('name', null, ['mapped' => false, 'constraints' => [
            new NotBlank(),
        ]]);
    }
}
```

Prepare a custom logic in Controller.

```php
    #[Rest\Post('/batch/variable/delete')]
    // TODO Rest API docs
    public function batchVariableDeleteAction(Request $request)
    {
        $process = function (Device $device, FormInterface $form) {
            $name = $form->get('name')->getData();

            // My custom logic
        };

        return $this->handleBatchForm($process, $request, DeviceDeny::VARIABLE_DELETE, null, BatchVariableDeleteType::class);
    }
```

Notable comment for `handleBatchForm` function.

```php
/**
 * Callable $process has following definition:
 * ($object, FormInterface $form): ?BatchResult.
 * Empty result from $process will be populated with getDefaultBatchProcessEmptyResult().
 * By default it will be BatchResult with success status.
 *
 * Callable $postProcess has following definition:
 * (array $objects, FormInterface $form): void.
 */
```

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

## REST API Documentation

**Note!** Only one method for each endpoint is supported. Multiple methods for endpoint might yield unexpected results (i.e. having both GET and POST on `/api/config`).

### Subject parameters

Some of mentioned attributes support subject parameters which means that a string (i.e. summary, description) can include parameters which will be replaced by `Describer\ApiDescriber`.

Subject parameters are prepared based on `subject` in `Api\Resource` attribute.

Supported subject parameters as follows. Example for `subject` = "User".

-   `subjectLower` i.e. "user"
-   `subjectTitle` i.e. "User"
-   `subjectPluralLower` i.e. "users"
-   `subjectPluralTitle` i.e. "Users"

### Attributes

-   `#[Api\Summary]` - Attaches summary to the operation. Summary supports subject parameters.
-   `#[Api\Parameter]` - Parameter with description that supports subject parameters.
-   `#[Api\ParameterPathId]` - Preconfigured path ID parameter with description that supports subject parameters.
-   `#[Api\RequestBody]` - Request body with description that supports subject parameters.
-   `#[Api\RequestBodyBatch]` - Request body with description that supports subject parameters. When there is no content (`Nelmio\ApiDocBundle\Annotation\Model` is expected) it set as Api\Resource->batchFormClass. It also attaches 'sorting_field_choices' to content options.
-   `#[Api\RequestBodyCreate]` - Request body with content set as Api\Resource->createFormClass and description that supports subject parameters.
-   `#[Api\RequestBodyEdit]` - Request body with content set as Api\Resource->editFormClass and description that supports subject parameters.
-   `#[Api\RequestBodyList]` - Request body with content set as Api\Resource->listFormClass (with 'sorting_field_choices' and 'filter_filterBy_choices' options) and description that supports subject parameters.
-   `#[Api\RequestBodyExportCsv]` - Request body with content set as Api\Resource->exportCsvFormClass (with 'sorting_field_choices', 'filter_filterBy_choices' and 'fields_field_choices' options) and description that supports subject parameters.
-   `#[Api\RequestBodyExportExcel]` - Request body with content set as Api\Resource->exportExcelFormClass (with 'sorting_field_choices', 'filter_filterBy_choices' and 'fields_field_choices' options) and description that supports subject parameters.
-   `#[Api\Response200]` - Preconfigured response with code 200 and description that supports subject parameters.
-   `#[Api\Response200BatchResults]` - Preconfigured list response with code 200 and description that supports subject parameters and sets content as array of `Carve\ApiBundle\Model\BatchResult`.
-   `#[Api\Response200Groups]` - Preconfigured response with code 200 and description that supports subject parameters and attaches serialization groups to content (`Nelmio\ApiDocBundle\Annotation\Model` is expected as content).
-   `#[Api\Response200SubjectGroups]` - Preconfigured response with code 200 and description that supports subject parameters and sets content as `Nelmio\ApiDocBundle\Annotation\Model` with subject class and serialization groups.
-   `#[Api\Response200List]` - Preconfigured list response with code 200 and description that supports subject parameters and sets content as object with `rowsCount` and `results` that include items with subject class and serialization groups.
-   `#[Api\Response204]` - Preconfigured response with code 204 and description that supports subject parameters.
-   `#[Api\Response204Delete]` - Preconfigured response with code 204 and default description (`{{ subjectTitle }} successfully deleted`) that supports subject parameters.
-   `#[Api\Response400]` - Preconfigured response with code 400 and default description (`Unable to process request due to invalid data`) that supports subject parameters.
-   `#[Api\Response404]` - Preconfigured response with code 404 and description that supports subject parameters.
-   `#[Api\Response404Id]` - Preconfigured response with code 404 and default description (`{{ subjectTitle }} with specified ID was not found`) that supports subject parameters.

WIP

Common use cases:

-   `#[OA\RequestBody(content: new Model(type: AuthenticatedChangePasswordType::class))]`

### Usage examples

```php
    #[Api\Summary('Get {{ subjectLower }} by ID')]
    public function getAction(int $id)
```

```php
    #[Api\ParameterPathId('ID of {{ subjectLower }} to return')]
    public function getAction(int $id)
```

```php
    #[Api\Parameter(name: 'serialNumber', in: 'path', schema: new OA\Schema(type: 'string'), description: 'The serial number of {{ subjectLower }} to return')]
    public function getAction(string $serialNumber)
```

```php
    #[Api\RequestBody(description: 'New data for {{ subjectTitle }}', content: new NA\Model(type: Order::class))]
    public function editAction()
```

```php
use Nelmio\ApiDocBundle\Annotation as NA;

    #[Api\RequestBodyBatch(content: new NA\Model(type: BatchVariableAddType::class))]
    public function batchVariableAddAction()
```

```php
use Nelmio\ApiDocBundle\Annotation as NA;

    #[Api\Response200(description: 'Returns public configuration for application', content: new NA\Model(type: PublicConfiguration::class))]
    public function getAction()
```

```php
use Nelmio\ApiDocBundle\Annotation as NA;

    #[Rest\View(serializerGroups: ['public:configuration'])]
    #[Api\Response200Groups(description: 'Returns public configuration for application', content: new NA\Model(type: PublicConfiguration::class))]
    public function getAction()
```

```php
use Nelmio\ApiDocBundle\Annotation as NA;

#[Rest\View(serializerGroups: ['public:configuration'])]
class AnonymousController extends AbstractApiController
{
    #[Api\Response200Groups(description: 'Returns public configuration for application', content: new NA\Model(type: PublicConfiguration::class))]
    public function getAction()
}
```

```php
    #[Api\Response200SubjectGroups('Returns created {{ subjectLower }}')]
    public function createAction(Request $request)
```

```php
    #[Api\Response200List('Returns list of {{ subjectPluralLower }}')]
    public function listAction(Request $request)
```

```php
    #[Api\Response204('{{ subjectTitle }} successfully enabled')]
    public function enableAction()
```

```php
    #[Api\Response404('{{ subjectTitle }} with specified serial number not found')]
    public function getAction()
```

### Common use cases

```php
use Carve\ApiBundle\Attribute as Api;
use Nelmio\ApiDocBundle\Annotation as NA;

    #[Rest\Post('/change/password')]
    #[Api\Summary('Change authenticated user password')]
    #[Api\Response204('Password successfully changed')]
    #[Api\RequestBody(content: new NA\Model(type: AuthenticatedChangePasswordType::class))]
    #[Api\Response400]
    public function changePasswordAction(Request $request)
```

```php
use Carve\ApiBundle\Attribute as Api;
use Nelmio\ApiDocBundle\Annotation as NA;

    #[Rest\Post('/change/password/required')]
    #[Api\Summary('Change authenticated user password when password change is required. Password change is required when authenticated user roles include ROLE_CHANGEPASSWORDREQUIRED')]
    #[Api\RequestBody(content: new NA\Model(type: AuthenticationChangePasswordRequiredType::class))]
    #[Api\Response200(description: 'Returns updated authentication data', content: new NA\Model(type: AuthenticationData::class))]
    #[Api\Response400]
```

```php
use Carve\ApiBundle\Attribute as Api;

    #[Rest\Get('/token/extend/{refreshTokenString}')]
    #[Api\Summary('Extend refresh token for another access token TTL')]
    #[Api\Response204('Correct refresh token extended successfully')]
    #[Api\Parameter(in: 'path', name: 'refreshTokenString', description: 'Refresh token string')]
    public function extendAction(string $refreshTokenString)
```

```php
use Carve\ApiBundle\Attribute as Api;
use Nelmio\ApiDocBundle\Annotation as NA;

    #[Rest\Post('/batch/variable/add')]
    #[Api\Summary('Add variable to multiple {{ subjectPluralLower }}')]
    #[Api\RequestBodyBatch(content: new NA\Model(type: BatchVariableAddType::class))]
    #[Api\Response200BatchResults]
    #[Api\Response400]
    public function batchVariableAddAction(Request $request)
```

```php
use OpenApi\Attributes as OA;

    #[Api\Response200(
        description: 'Progress',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'total', type: 'integer'),
                new OA\Property(property: 'pending', type: 'integer'),
            ]
        ),
    )]
    public function progressAction(int $id)
```
