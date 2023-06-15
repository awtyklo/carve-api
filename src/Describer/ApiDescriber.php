<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Describer;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Helper\MessageParameterNormalizer;
use Carve\ApiBundle\Service\ApiResourceManager;
use Nelmio\ApiDocBundle\Annotation as NA;
use Nelmio\ApiDocBundle\OpenApiPhp\Util;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberInterface;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberTrait;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use Symfony\Component\Routing\Route;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

class ApiDescriber implements RouteDescriberInterface
{
    use RouteDescriberTrait;

    protected ApiResourceManager $apiResourceManager;

    public function __construct(ApiResourceManager $apiResourceManager)
    {
        $this->apiResourceManager = $apiResourceManager;
    }

    public function describe(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->describeSummary($api, $route, $reflectionMethod);
        $this->describeParameter($api, $route, $reflectionMethod);
        $this->describeParameterPathId($api, $route, $reflectionMethod);

        $this->describeResponse200($api, $route, $reflectionMethod);
        $this->describeResponse200BatchResults($api, $route, $reflectionMethod);
        $this->describeResponse200Groups($api, $route, $reflectionMethod);
        $this->describeResponse200SubjectGroups($api, $route, $reflectionMethod);
        $this->describeResponse200List($api, $route, $reflectionMethod);
        $this->describeResponse204($api, $route, $reflectionMethod);
        $this->describeResponse204Delete($api, $route, $reflectionMethod);
        $this->describeResponse400($api, $route, $reflectionMethod);
        $this->describeResponse404($api, $route, $reflectionMethod);
        $this->describeResponse404Id($api, $route, $reflectionMethod);

        $this->describeRequestBody($api, $route, $reflectionMethod);
        $this->describeRequestBodyBatch($api, $route, $reflectionMethod);
        $this->describeRequestBodyCreate($api, $route, $reflectionMethod);
        $this->describeRequestBodyEdit($api, $route, $reflectionMethod);
        $this->describeRequestBodyList($api, $route, $reflectionMethod);
        $this->describeRequestBodyExportCsv($api, $route, $reflectionMethod);
        $this->describeRequestBodyExportExcel($api, $route, $reflectionMethod);
    }

    protected function describeSummary(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $attribute = $this->getAttribute($reflectionMethod, Api\Summary::class);
        if (!$attribute) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $operation->summary = $this->applySubjectParameters($reflectionMethod, $attribute->getSummary());
        }
    }

    protected function describeParameter(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $attribute = $this->getAttribute($reflectionMethod, Api\Parameter::class);
        if (!$attribute) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $oaParameter = $this->findOpenApiParameter($route, $operation);
            $oaParameter->description = $this->applySubjectParameters($reflectionMethod, $oaParameter->description);
        }
    }

    protected function describeParameterPathId(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $attribute = $this->getAttribute($reflectionMethod, Api\ParameterPathId::class);
        if (!$attribute) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $oaParameter = $this->findOpenApiParameter($route, $operation);
            $oaParameter->description = $this->applySubjectParameters($reflectionMethod, $oaParameter->description);
        }
    }

    protected function getResponse(string $class, OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod): ?OA\Response
    {
        if (!$this->hasAttribute($reflectionMethod, $class)) {
            return null;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $response = $this->findResponse($operation, $class);

            if ($response) {
                return $response;
            }
        }

        return null;
    }

    protected function applyResponseSubjectParameters(OA\Response $response, \ReflectionMethod $reflectionMethod)
    {
        $response->description = $this->applySubjectParameters($reflectionMethod, $response->description);
    }

    protected function findAndApplyResponseSubjectParameters(string $class, OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod): ?OA\Response
    {
        $response = $this->getResponse($class, $api, $route, $reflectionMethod);
        if (null === $response) {
            return null;
        }

        $this->applyResponseSubjectParameters($response, $reflectionMethod);

        return $response;
    }

    protected function describeResponse200(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->findAndApplyResponseSubjectParameters(Api\Response200::class, $api, $route, $reflectionMethod);
    }

    protected function describeResponse200BatchResults(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->findAndApplyResponseSubjectParameters(Api\Response200BatchResults::class, $api, $route, $reflectionMethod);
    }

    protected function describeResponse200Groups(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $response = $this->findAndApplyResponseSubjectParameters(Api\Response200Groups::class, $api, $route, $reflectionMethod);
        if (null === $response) {
            return;
        }

        // Find Nelmio\ApiDocBundle\Annotation\Model in attachebles and attach groups
        if (Generator::UNDEFINED !== $response->attachables) {
            foreach ($response->attachables as $attachable) {
                if ($attachable instanceof NA\Model) {
                    $attachable->groups = $this->getSerializerGroups($reflectionMethod);
                }
            }
        }
    }

    protected function describeResponse200SubjectGroups(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $response = $this->findAndApplyResponseSubjectParameters(Api\Response200SubjectGroups::class, $api, $route, $reflectionMethod);
        if (null === $response) {
            return;
        }

        $class = $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'class');
        if (null === $class) {
            return;
        }

        $attachable = new NA\Model(type: $class, groups: $this->getSerializerGroups($reflectionMethod));

        // Add Nelmio\ApiDocBundle\Annotation\Model to attachebles
        if (Generator::UNDEFINED === $response->attachables) {
            $response->attachables = [$attachable];
        } else {
            $response->attachables[] = $attachable;
        }
    }

    protected function describeResponse204(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->findAndApplyResponseSubjectParameters(Api\Response204::class, $api, $route, $reflectionMethod);
    }

    protected function describeResponse204Delete(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->findAndApplyResponseSubjectParameters(Api\Response204Delete::class, $api, $route, $reflectionMethod);
    }

    protected function describeResponse400(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->findAndApplyResponseSubjectParameters(Api\Response400::class, $api, $route, $reflectionMethod);
    }

    protected function describeResponse404(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->findAndApplyResponseSubjectParameters(Api\Response404::class, $api, $route, $reflectionMethod);
    }

    protected function describeResponse404Id(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->findAndApplyResponseSubjectParameters(Api\Response404Id::class, $api, $route, $reflectionMethod);
    }

    protected function getRequestBody(string $class, OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod): ?OA\RequestBody
    {
        if (!$this->hasAttribute($reflectionMethod, $class)) {
            return null;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            if ($operation->requestBody) {
                return $operation->requestBody;
            }
        }

        return null;
    }

    protected function applyRequestBodySubjectParameters(OA\RequestBody $requestBody, \ReflectionMethod $reflectionMethod)
    {
        $requestBody->description = $this->applySubjectParameters($reflectionMethod, $requestBody->description);
    }

    protected function findAndApplyRequestBodySubjectParameters(string $class, OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod): ?OA\RequestBody
    {
        $requestBody = $this->getRequestBody($class, $api, $route, $reflectionMethod);
        if (null === $requestBody) {
            return null;
        }

        $this->applyRequestBodySubjectParameters($requestBody, $reflectionMethod);

        return $requestBody;
    }

    protected function describeRequestBody(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->findAndApplyRequestBodySubjectParameters(Api\RequestBody::class, $api, $route, $reflectionMethod);
    }

    protected function describeRequestBodyCreate(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $requestBody = $this->findAndApplyRequestBodySubjectParameters(Api\RequestBodyCreate::class, $api, $route, $reflectionMethod);
        if (null === $requestBody) {
            return;
        }

        $createFormClass = $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'createFormClass');
        if (null === $createFormClass) {
            return;
        }

        $attachable = new NA\Model(type: $createFormClass);

        // We add Nelmio\ApiDocBundle\Annotation\Model to attachebles of requestBody
        if (Generator::UNDEFINED === $requestBody->attachables) {
            $requestBody->attachables = [$attachable];
        } else {
            $requestBody->attachables[] = $attachable;
        }
    }

    protected function describeRequestBodyBatch(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $requestBody = $this->findAndApplyRequestBodySubjectParameters(Api\RequestBodyBatch::class, $api, $route, $reflectionMethod);
        if (null === $requestBody) {
            return;
        }

        $attached = false;

        // Find Nelmio\ApiDocBundle\Annotation\Model in attachebles and attach 'sorting_field_choices'
        if (Generator::UNDEFINED !== $requestBody->attachables) {
            foreach ($requestBody->attachables as $attachable) {
                if ($attachable instanceof NA\Model) {
                    $attachable->options['sorting_field_choices'] = $this->getSortingFieldChoices($reflectionMethod);
                    $attached = true;
                }
            }
        }

        if (!$attached) {
            $batchFormClass = $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'batchFormClass');
            if (null === $batchFormClass) {
                return;
            }

            $attachable = new NA\Model(type: $batchFormClass, options: ['sorting_field_choices' => $this->getSortingFieldChoices($reflectionMethod)]);

            // We add Nelmio\ApiDocBundle\Annotation\Model to attachebles of requestBody
            if (Generator::UNDEFINED === $requestBody->attachables) {
                $requestBody->attachables = [$attachable];
            } else {
                $requestBody->attachables[] = $attachable;
            }
        }
    }

    protected function describeRequestBodyEdit(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $requestBody = $this->findAndApplyRequestBodySubjectParameters(Api\RequestBodyEdit::class, $api, $route, $reflectionMethod);
        if (null === $requestBody) {
            return;
        }

        $editFormClass = $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'editFormClass');
        if (null === $editFormClass) {
            return;
        }

        $attachable = new NA\Model(type: $editFormClass);

        // We add Nelmio\ApiDocBundle\Annotation\Model to attachebles of requestBody
        if (Generator::UNDEFINED === $requestBody->attachables) {
            $requestBody->attachables = [$attachable];
        } else {
            $requestBody->attachables[] = $attachable;
        }
    }

    protected function describeRequestBodyList(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $requestBody = $this->findAndApplyRequestBodySubjectParameters(Api\RequestBodyList::class, $api, $route, $reflectionMethod);
        if (null === $requestBody) {
            return;
        }

        $listFormClass = $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'listFormClass');
        if (null === $listFormClass) {
            return;
        }

        $sortingFieldChoices = $this->getSortingFieldChoices($reflectionMethod);
        $filterByChoices = $this->getFilterFilterByChoices($reflectionMethod);

        // This value is used only to force generating different schemas for different endpoints for form class
        $uniqueDocumentationGroup = md5(serialize($sortingFieldChoices).serialize($filterByChoices));

        $attachable = new NA\Model(
            groups: [$uniqueDocumentationGroup],
            type: $listFormClass,
            options: [
                'sorting_field_choices' => $sortingFieldChoices,
                'filter_filterBy_choices' => $filterByChoices,
            ],
        );

        // We add Nelmio\ApiDocBundle\Annotation\Model to attachebles of requestBody
        if (Generator::UNDEFINED === $requestBody->attachables) {
            $requestBody->attachables = [$attachable];
        } else {
            $requestBody->attachables[] = $attachable;
        }
    }

    protected function describeRequestBodyExportCsv(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $requestBody = $this->findAndApplyRequestBodySubjectParameters(Api\RequestBodyExportCsv::class, $api, $route, $reflectionMethod);
        if (null === $requestBody) {
            return;
        }

        $exportCsvFormClass = $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'exportCsvFormClass');
        if (null === $exportCsvFormClass) {
            return;
        }

        $sortingFieldChoices = $this->getSortingFieldChoices($reflectionMethod);
        $filterByChoices = $this->getFilterFilterByChoices($reflectionMethod);
        $fieldsFieldChoices = $this->getFieldsFieldChoices($reflectionMethod);

        // This value is used only to force generating different schemas for different endpoints for form class
        $uniqueDocumentationGroup = md5(serialize($sortingFieldChoices).serialize($filterByChoices));

        $attachable = new NA\Model(
            groups: [$uniqueDocumentationGroup],
            type: $exportCsvFormClass,
            options: [
                'sorting_field_choices' => $sortingFieldChoices,
                'filter_filterBy_choices' => $filterByChoices,
                'fields_field_choices' => $fieldsFieldChoices,
            ],
        );

        // We add Nelmio\ApiDocBundle\Annotation\Model to attachebles of requestBody
        if (Generator::UNDEFINED === $requestBody->attachables) {
            $requestBody->attachables = [$attachable];
        } else {
            $requestBody->attachables[] = $attachable;
        }
    }

    protected function describeRequestBodyExportExcel(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $requestBody = $this->findAndApplyRequestBodySubjectParameters(Api\RequestBodyExportExcel::class, $api, $route, $reflectionMethod);
        if (null === $requestBody) {
            return;
        }

        $exportExcelFormClass = $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'exportExcelFormClass');
        if (null === $exportExcelFormClass) {
            return;
        }

        $sortingFieldChoices = $this->getSortingFieldChoices($reflectionMethod);
        $filterByChoices = $this->getFilterFilterByChoices($reflectionMethod);
        $fieldsFieldChoices = $this->getFieldsFieldChoices($reflectionMethod);

        // This value is used only to force generating different schemas for different endpoints for form class
        $uniqueDocumentationGroup = md5(serialize($sortingFieldChoices).serialize($filterByChoices));

        $attachable = new NA\Model(
            groups: [$uniqueDocumentationGroup],
            type: $exportExcelFormClass,
            options: [
                'sorting_field_choices' => $sortingFieldChoices,
                'filter_filterBy_choices' => $filterByChoices,
                'fields_field_choices' => $fieldsFieldChoices,
            ],
        );

        // We add Nelmio\ApiDocBundle\Annotation\Model to attachebles of requestBody
        if (Generator::UNDEFINED === $requestBody->attachables) {
            $requestBody->attachables = [$attachable];
        } else {
            $requestBody->attachables[] = $attachable;
        }
    }

    protected function describeResponse200List(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\Response200List::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $response = $this->findResponse($operation, Api\Response200List::class);
            if (!$response) {
                continue;
            }

            $response->description = $this->applySubjectParameters($reflectionMethod, $response->description);

            // I do not know how to adjust this to similar idea as Response200, Response200Groups or Response200SubjectGroups
            foreach ($response->content as $content) {
                if ($content instanceof OA\MediaType) {
                    $resultsProperty = Util::getProperty($content->schema, 'results');

                    $class = $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'class');
                    if (null === $class) {
                        continue;
                    }

                    $resultsProperty->items->ref->type = $class;

                    $serializerGroups = $this->getSerializerGroups($reflectionMethod);
                    if (null !== $serializerGroups) {
                        $resultsProperty->items->ref->groups = $serializerGroups;
                    }
                }
            }
        }
    }

    protected function findOpenApiParameter(Route $route, OA\Operation $operation): ?OA\Parameter
    {
        $requirements = $route->getRequirements();
        $compiledRoute = $route->compile();

        foreach ($compiledRoute->getPathVariables() as $pathVariable) {
            if ('_format' === $pathVariable) {
                continue;
            }

            return Util::getOperationParameter($operation, $pathVariable, 'path');
        }

        return null;
    }

    protected function findResponse(OA\Operation $operation, string $class): ?OA\Response
    {
        if (Generator::UNDEFINED === $operation->responses) {
            return null;
        }

        foreach ($operation->responses as $oaResponse) {
            if ($oaResponse instanceof $class) {
                return $oaResponse;
            }
        }

        return null;
    }

    protected function applySubjectParameters(\ReflectionMethod $reflectionMethod, string $message): string
    {
        if (!$this->apiResourceManager->hasAttributeArgument($reflectionMethod, 'subject')) {
            return $message;
        }

        return MessageParameterNormalizer::applyParameters($message, [
            'subjectLower' => $this->getSubjectLower($reflectionMethod),
            'subjectTitle' => $this->getSubjectTitle($reflectionMethod),
            'subjectPluralTitle' => $this->getSubjectPluralTitle($reflectionMethod),
            'subjectPluralLower' => $this->getSubjectPluralLower($reflectionMethod),
        ]);
    }

    protected function getSubjectTitle(\ReflectionMethod $reflectionMethod): string
    {
        return (string) u($this->getSubject($reflectionMethod))->title();
    }

    protected function getSubjectLower(\ReflectionMethod $reflectionMethod): string
    {
        return (string) u($this->getSubject($reflectionMethod))->lower();
    }

    protected function getSubjectPluralLower(\ReflectionMethod $reflectionMethod): string
    {
        $subject = $this->getSubjectLower($reflectionMethod);
        $inflector = new EnglishInflector();
        $plurals = $inflector->pluralize($subject);

        return $plurals[0] ?? $subject;
    }

    protected function getSubjectPluralTitle(\ReflectionMethod $reflectionMethod): string
    {
        $subject = $this->getSubjectTitle($reflectionMethod);
        $inflector = new EnglishInflector();
        $plurals = $inflector->pluralize($subject);

        return $plurals[0] ?? $subject;
    }

    protected function getSubject(\ReflectionMethod $reflectionMethod): string
    {
        return $this->apiResourceManager->getAttributeArgument($reflectionMethod, 'subject');
    }

    protected function getAttribute(\ReflectionMethod $reflectionMethod, string $attributeClass): ?object
    {
        $attributes = $reflectionMethod->getAttributes($attributeClass);
        if (0 === count($attributes)) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    protected function hasAttribute(\ReflectionMethod $reflectionMethod, string $attributeClass): bool
    {
        return count($reflectionMethod->getAttributes($attributeClass)) > 0;
    }

    protected function getSerializerGroups(\ReflectionMethod $reflectionMethod): ?array
    {
        return $this->apiResourceManager->getSerializerGroups($reflectionMethod);
    }

    protected function getSortingFieldChoices(\ReflectionMethod $reflectionMethod): array
    {
        return $this->apiResourceManager->getSortingFieldChoices($reflectionMethod);
    }

    protected function getFilterFilterByChoices(\ReflectionMethod $reflectionMethod): array
    {
        return $this->apiResourceManager->getFilterFilterByChoices($reflectionMethod);
    }

    protected function getFieldsFieldChoices(\ReflectionMethod $reflectionMethod): array
    {
        return $this->apiResourceManager->getFieldsFieldChoices($reflectionMethod);
    }
}
