<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Describer;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Controller\AbstractApiController;
use Carve\ApiBundle\Helper\MessageParameterNormalizer;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as NA;
use Nelmio\ApiDocBundle\OpenApiPhp\Util;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberInterface;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberTrait;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\Routing\Route;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

class ApiDescriber implements RouteDescriberInterface
{
    use RouteDescriberTrait;

    protected $serializerExtractor;

    public function __construct(SerializerExtractor $serializerExtractor)
    {
        $this->serializerExtractor = $serializerExtractor;
    }

    public function describe(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->describeSummary($api, $route, $reflectionMethod);
        $this->describeParameter($api, $route, $reflectionMethod);
        $this->describeParameterPathId($api, $route, $reflectionMethod);

        $this->describeResponse200($api, $route, $reflectionMethod);
        $this->describeResponse200Groups($api, $route, $reflectionMethod);
        $this->describeResponse200SubjectGroups($api, $route, $reflectionMethod);
        $this->describeResponse204($api, $route, $reflectionMethod);
        $this->describeResponse204Delete($api, $route, $reflectionMethod);
        $this->describeResponse400($api, $route, $reflectionMethod);
        $this->describeResponse404($api, $route, $reflectionMethod);
        $this->describeResponse404Id($api, $route, $reflectionMethod);

        $this->describeRequestBodyCreate($api, $route, $reflectionMethod);
        $this->describeRequestBodyEdit($api, $route, $reflectionMethod);

        $this->describeListRequestBody($api, $route, $reflectionMethod);
        $this->describeResponse200List($api, $route, $reflectionMethod);
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

    protected function describeResponse200Groups(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $response = $this->findAndApplyResponseSubjectParameters(Api\Response200Groups::class, $api, $route, $reflectionMethod);
        if (null === $response) {
            return null;
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
            return null;
        }

        $attachable = new NA\Model(type: $this->getClass($reflectionMethod), groups: $this->getSerializerGroups($reflectionMethod));

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

    protected function describeRequestBody(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\RequestBody::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $operation->requestBody->description = $this->applySubjectParameters($reflectionMethod, $operation->requestBody->description);
        }
    }

    protected function describeRequestBodyCreate(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\RequestBodyCreate::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $attachable = new NA\Model(type: $this->getCreateFormClass($reflectionMethod));

            $operation->requestBody->description = $this->applySubjectParameters($reflectionMethod, $operation->requestBody->description);

            // We add Nelmio\ApiDocBundle\Annotation\Model to attachebles of requestBody
            if (Generator::UNDEFINED === $operation->requestBody->attachables) {
                $operation->requestBody->attachables = [$attachable];
            } else {
                $operation->requestBody->attachables[] = $attachable;
            }
        }
    }

    protected function describeRequestBodyEdit(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\RequestBodyEdit::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $attachable = new NA\Model(type: $this->getEditFormClass($reflectionMethod));

            $operation->requestBody->description = $this->applySubjectParameters($reflectionMethod, $operation->requestBody->description);

            // We add Nelmio\ApiDocBundle\Annotation\Model to attachebles of requestBody
            if (Generator::UNDEFINED === $operation->requestBody->attachables) {
                $operation->requestBody->attachables = [$attachable];
            } else {
                $operation->requestBody->attachables[] = $attachable;
            }
        }
    }

    protected function describeListRequestBody(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\ListRequestBody::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            // Unfortunately I do not know why this is in "_unmerged" or how to properly set it up
            foreach ($operation->requestBody->_unmerged as $unmerged) {
                if ($unmerged instanceof OA\JsonContent) {
                    $class = $this->getClass($reflectionMethod);
                    $defaultSerializerGroups = $this->getSerializerGroups($reflectionMethod);

                    $sortingSerializerGroups = $this->getListFormSortingFieldGroups($reflectionMethod);
                    if (Generator::UNDEFINED === $sortingSerializerGroups) {
                        if (null !== $defaultSerializerGroups) {
                            $sortingSerializerGroups = AbstractApiController::normalizeDefaultSerializerGroups($defaultSerializerGroups);
                        }
                    }

                    $sortingFieldChoices = $this->serializerExtractor->getProperties($class, ['serializer_groups' => $sortingSerializerGroups]);
                    // Remove 'deny' sortingField choices
                    if (($denyKey = array_search('deny', $sortingFieldChoices)) !== false) {
                        unset($sortingFieldChoices[$denyKey]);
                    }

                    $sortingFieldAppend = $this->getListFormSortingFieldAppend($reflectionMethod);
                    if (Generator::UNDEFINED !== $sortingFieldAppend) {
                        $sortingFieldChoices = AbstractApiController::appendFieldChoice($sortingFieldChoices, $sortingFieldAppend);
                    }

                    $filterBySerializerGroups = $this->getListFormFilterByGroups($reflectionMethod);
                    if (Generator::UNDEFINED === $filterBySerializerGroups) {
                        if (null !== $defaultSerializerGroups) {
                            $filterBySerializerGroups = AbstractApiController::normalizeDefaultSerializerGroups($defaultSerializerGroups);
                        }
                    }

                    $filterByChoices = $this->serializerExtractor->getProperties($class, ['serializer_groups' => $filterBySerializerGroups]);
                    // Remove 'deny' from filterBy choices
                    if (($denyKey = array_search('deny', $filterByChoices)) !== false) {
                        unset($filterByChoices[$denyKey]);
                    }

                    $filterByAppend = $this->getListFormFilterByAppend($reflectionMethod);
                    if (Generator::UNDEFINED !== $filterByAppend) {
                        $filterByChoices = AbstractApiController::appendFieldChoice($filterByChoices, $filterByAppend);
                    }

                    // This value is used only to force generating different schemas for different endpoints for
                    // ListQueryType / ListQuerySortingType / ListQueryFilterType
                    $uniqueDocumentationGroup = md5(serialize($sortingFieldChoices).serialize($filterByChoices));

                    $unmerged->ref = new NA\Model(groups: [$uniqueDocumentationGroup], type: $this->getListFormClass($reflectionMethod), options: [
                        'sorting_field_choices' => $sortingFieldChoices,
                        'filter_filterBy_choices' => $filterByChoices,
                        'documentation' => [
                            'groups' => [$uniqueDocumentationGroup],
                        ],
                    ]);
                }
            }
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
                    $resultsProperty->items->ref->type = $this->getClass($reflectionMethod);

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
        if (!$this->hasApiResourceSubject($reflectionMethod)) {
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
        return $this->getApiResourceProperty($reflectionMethod, 'subject');
    }

    protected function getClass(\ReflectionMethod $reflectionMethod): string
    {
        return $this->getApiResourceProperty($reflectionMethod, 'class');
    }

    protected function getCreateFormClass(\ReflectionMethod $reflectionMethod): string
    {
        return $this->getApiResourceProperty($reflectionMethod, 'createFormClass');
    }

    protected function getEditFormClass(\ReflectionMethod $reflectionMethod): string
    {
        return $this->getApiResourceProperty($reflectionMethod, 'editFormClass');
    }

    protected function getListFormClass(\ReflectionMethod $reflectionMethod): string
    {
        return $this->getApiResourceProperty($reflectionMethod, 'listFormClass');
    }

    protected function getListFormSortingFieldGroups(\ReflectionMethod $reflectionMethod): string|array
    {
        return $this->getApiResourceProperty($reflectionMethod, 'listFormSortingFieldGroups');
    }

    protected function getListFormSortingFieldAppend(\ReflectionMethod $reflectionMethod): string|array
    {
        return $this->getApiResourceProperty($reflectionMethod, 'listFormSortingFieldAppend');
    }

    protected function getListFormFilterByGroups(\ReflectionMethod $reflectionMethod): string|array
    {
        return $this->getApiResourceProperty($reflectionMethod, 'listFormFilterByGroups');
    }

    protected function getListFormFilterByAppend(\ReflectionMethod $reflectionMethod): string|array
    {
        return $this->getApiResourceProperty($reflectionMethod, 'listFormFilterByAppend');
    }

    protected function getApiResourceProperty(\ReflectionMethod $reflectionMethod, string $propertyName): string|array
    {
        $reflectionClass = new \ReflectionClass($reflectionMethod->class);
        foreach ($reflectionClass->getAttributes(Api\Resource::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();

            return $attributeInstance->$propertyName;
        }

        throw new \Exception('Missing property "'.$propertyName.'". Please make sure that "'.$reflectionMethod->class.'" has "Carve\ApiBundle\Attribute\Api\Resource" attribute');
    }

    protected function hasApiResourceProperty(\ReflectionMethod $reflectionMethod, string $propertyName): bool
    {
        $reflectionClass = new \ReflectionClass($reflectionMethod->class);
        foreach ($reflectionClass->getAttributes(Api\Resource::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();

            if ($attributeInstance->$propertyName) {
                return true;
            }
        }

        return false;
    }

    protected function hasApiResourceSubject(\ReflectionMethod $reflectionMethod): bool
    {
        return $this->hasApiResourceProperty($reflectionMethod, 'subject');
    }

    protected function getSerializerGroups(\ReflectionMethod $reflectionMethod): ?array
    {
        foreach ($reflectionMethod->getAttributes(Rest\View::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();
            $serializerGroups = $attributeInstance->getSerializerGroups();

            return count($serializerGroups) > 0 ? $serializerGroups : null;
        }

        $reflectionClass = new \ReflectionClass($reflectionMethod->class);
        foreach ($reflectionClass->getAttributes(Rest\View::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();
            $serializerGroups = $attributeInstance->getSerializerGroups();

            return count($serializerGroups) > 0 ? $serializerGroups : null;
        }

        // Additionally check parent class
        $reflectionParentClass = $reflectionClass->getParentClass();
        foreach ($reflectionParentClass->getAttributes(Rest\View::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();
            $serializerGroups = $attributeInstance->getSerializerGroups();

            return count($serializerGroups) > 0 ? $serializerGroups : null;
        }

        return null;
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
}
