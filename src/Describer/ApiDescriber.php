<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Describer;

use Carve\ApiBundle\Attribute as Api;
use FOS\RestBundle\Controller\Annotations as Rest;
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

    public function describe(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        $this->describeResponse404($api, $route, $reflectionMethod);

        $this->describeCreateDescription($api, $route, $reflectionMethod);
        $this->describeCreateRequestBody($api, $route, $reflectionMethod);
        $this->describeCreateResponse200($api, $route, $reflectionMethod);

        $this->describeDeleteIdParameter($api, $route, $reflectionMethod);
        $this->describeDeleteDescription($api, $route, $reflectionMethod);
        $this->describeDeleteResponse204($api, $route, $reflectionMethod);

        $this->describeEditIdParameter($api, $route, $reflectionMethod);
        $this->describeEditDescription($api, $route, $reflectionMethod);
        $this->describeEditRequestBody($api, $route, $reflectionMethod);
        $this->describeEditResponse200($api, $route, $reflectionMethod);

        $this->describeGetIdParameter($api, $route, $reflectionMethod);
        $this->describeGetDescription($api, $route, $reflectionMethod);
        $this->describeGetResponse200($api, $route, $reflectionMethod);

        $this->describeListDescription($api, $route, $reflectionMethod);
        $this->describeListRequestBody($api, $route, $reflectionMethod);
        $this->describeListResponse200($api, $route, $reflectionMethod);
    }

    protected function describeResponse404(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\Response404::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $response = $this->findResponse($operation, Api\Response404::class);
            $response->description = $this->getSubjectTitle($reflectionMethod).' with specified ID was not found';
        }
    }

    protected function describeCreateDescription(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\CreateDescription::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $operation->summary = 'Create '.$this->getSubjectLower($reflectionMethod);
        }
    }

    protected function describeCreateRequestBody(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\CreateRequestBody::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            // Unfortunately I do not know why this is in "_unmerged" or how to properly set it up
            foreach ($operation->requestBody->_unmerged as $unmerged) {
                if ($unmerged instanceof OA\JsonContent) {
                    $unmerged->ref = new NA\Model(type: $this->getCreateFormClass($reflectionMethod));
                }
            }
        }
    }

    protected function describeCreateResponse200(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\CreateResponse200::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $response = $this->findResponse($operation, Api\CreateResponse200::class);
            $response->description = 'Returns created '.$this->getSubjectLower($reflectionMethod);

            // Unfortunately I do not know why this is in "_unmerged" or how to properly set it up
            foreach ($response->_unmerged as $unmerged) {
                if ($unmerged instanceof NA\Model) {
                    $unmerged->type = $this->getClass($reflectionMethod);

                    $serializerGroups = $this->getSerializerGroups($reflectionMethod);
                    if (null !== $serializerGroups) {
                        $unmerged->groups = $serializerGroups;
                    }
                }
            }
        }
    }

    protected function describeDeleteIdParameter(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\DeleteIdParameter::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $oaParameter = $this->findOpenApiParameter($route, $operation);
            $oaParameter->description = 'The ID of '.$this->getSubjectLower($reflectionMethod).' to delete';
        }
    }

    protected function describeDeleteDescription(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\DeleteDescription::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $operation->summary = 'Delete '.$this->getSubjectLower($reflectionMethod).' by ID';
        }
    }

    protected function describeDeleteResponse204(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\DeleteResponse204::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $response = $this->findResponse($operation, Api\DeleteResponse204::class);
            $response->description = $this->getSubjectTitle($reflectionMethod).' successfully deleted';
        }
    }

    protected function describeEditIdParameter(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\EditIdParameter::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $oaParameter = $this->findOpenApiParameter($route, $operation);
            $oaParameter->description = 'The ID of '.$this->getSubjectLower($reflectionMethod).' to edit';
        }
    }

    protected function describeEditDescription(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\EditDescription::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $operation->summary = 'Edit '.$this->getSubjectLower($reflectionMethod).' by ID';
        }
    }

    protected function describeEditRequestBody(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\EditRequestBody::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            // Unfortunately I do not know why this is in "_unmerged" or how to properly set it up
            foreach ($operation->requestBody->_unmerged as $unmerged) {
                if ($unmerged instanceof OA\JsonContent) {
                    $unmerged->ref = new NA\Model(type: $this->getEditFormClass($reflectionMethod));
                }
            }
        }
    }

    protected function describeEditResponse200(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\EditResponse200::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $response = $this->findResponse($operation, Api\EditResponse200::class);
            $response->description = 'Returns edited '.$this->getSubjectLower($reflectionMethod);

            // Unfortunately I do not know why this is in "_unmerged" or how to properly set it up
            foreach ($response->_unmerged as $unmerged) {
                if ($unmerged instanceof NA\Model) {
                    $unmerged->type = $this->getClass($reflectionMethod);

                    $serializerGroups = $this->getSerializerGroups($reflectionMethod);
                    if (null !== $serializerGroups) {
                        $unmerged->groups = $serializerGroups;
                    }
                }
            }
        }
    }

    protected function describeGetIdParameter(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\GetIdParameter::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $oaParameter = $this->findOpenApiParameter($route, $operation);
            $oaParameter->description = 'The ID of '.$this->getSubjectLower($reflectionMethod).' to return';
        }
    }

    protected function describeGetDescription(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\GetDescription::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $operation->summary = 'Get '.$this->getSubjectLower($reflectionMethod).' by ID';
        }
    }

    protected function describeGetResponse200(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\GetResponse200::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $response = $this->findResponse($operation, Api\GetResponse200::class);
            $response->description = 'Returns '.$this->getSubjectLower($reflectionMethod);

            // Unfortunately I do not know why this is in "_unmerged" or how to properly set it up
            foreach ($response->_unmerged as $unmerged) {
                if ($unmerged instanceof NA\Model) {
                    $unmerged->type = $this->getClass($reflectionMethod);

                    $serializerGroups = $this->getSerializerGroups($reflectionMethod);
                    if (null !== $serializerGroups) {
                        $unmerged->groups = $serializerGroups;
                    }
                }
            }
        }
    }

    protected function describeListDescription(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\ListDescription::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $operation->summary = 'List '.$this->getSubjectPlural($reflectionMethod);
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
                    $unmerged->ref = new NA\Model(type: $this->getListFormClass($reflectionMethod));
                }
            }
        }
    }

    protected function describeListResponse200(OA\OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (!$this->hasAttribute($reflectionMethod, Api\ListResponse200::class)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $response = $this->findResponse($operation, Api\ListResponse200::class);
            $response->description = 'Returns list of '.$this->getSubjectPlural($reflectionMethod);

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

    protected function getSubjectTitle(\ReflectionMethod $reflectionMethod): string
    {
        return (string) u($this->getSubject($reflectionMethod))->title();
    }

    protected function getSubjectLower(\ReflectionMethod $reflectionMethod): string
    {
        return (string) u($this->getSubject($reflectionMethod))->lower();
    }

    protected function getSubjectPlural(\ReflectionMethod $reflectionMethod): string
    {
        $subject = $this->getSubject($reflectionMethod);
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

    protected function getApiResourceProperty(\ReflectionMethod $reflectionMethod, string $propertyName): string
    {
        $reflectionClass = new \ReflectionClass($reflectionMethod->class);
        foreach ($reflectionClass->getAttributes(Api\Resource::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();

            return $attributeInstance->$propertyName;
        }

        throw new \Exception('Missing '.$propertyName);
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

    protected function hasAttribute(\ReflectionMethod $reflectionMethod, string $attributeClass): bool
    {
        return count($reflectionMethod->getAttributes($attributeClass)) > 0;
    }
}
