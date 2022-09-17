<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use Carve\ApiBundle\Form\ListQueryType;
use OpenApi\Annotations as OAA;
use OpenApi\Attributes as OA;
use OpenApi\Generator;

use function Symfony\Component\String\u;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Resource extends OAA\Tag
{
    public $class = Generator::UNDEFINED;
    public $denyClass = Generator::UNDEFINED;
    public $createFormClass = Generator::UNDEFINED;
    public $editFormClass = Generator::UNDEFINED;
    public $listFormClass = Generator::UNDEFINED;
    public $listFormSortingFieldGroups = Generator::UNDEFINED;
    public $listFormSortingFieldAppend = Generator::UNDEFINED;
    public $listFormFilterByGroups = Generator::UNDEFINED;
    public $listFormFilterByAppend = Generator::UNDEFINED;
    public $subject = Generator::UNDEFINED;

    /**
     * {@inheritdoc}
     */
    public static $_required = ['class'];

    /**
     * {@inheritdoc}
     */
    public static $_types = [
        'class' => 'string',
        'denyClass' => 'string',
        'createFormClass' => 'string',
        'editFormClass' => 'string',
        'listFormClass' => 'string',
        'listFormSortingFieldGroups' => 'array',
        'listFormSortingFieldAppend' => 'array',
        'listFormFilterByGroups' => 'array',
        'listFormFilterByAppend' => 'array',
        'subject' => 'string',
    ];

    public function __construct(
        string $class,
        ?string $denyClass = null,
        ?string $createFormClass = null,
        ?string $editFormClass = null,
        ?string $listFormClass = ListQueryType::class,
        ?array $listFormSortingFieldGroups = null,
        ?array $listFormSortingFieldAppend = null,
        ?array $listFormFilterByGroups = null,
        ?array $listFormFilterByAppend = null,
        ?string $subject = null,
        bool|string $tag = true,
        ?OA\ExternalDocumentation $externalDocs = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        $properties = [
            'class' => $class,
            'subject' => null === $subject ? $this->guessSubject($class) : $subject,
            'denyClass' => $denyClass ?? Generator::UNDEFINED,
            'createFormClass' => $createFormClass ?? Generator::UNDEFINED,
            'editFormClass' => $editFormClass ?? Generator::UNDEFINED,
            'listFormClass' => $listFormClass ?? Generator::UNDEFINED,
            'listFormSortingFieldGroups' => $listFormSortingFieldGroups ?? Generator::UNDEFINED,
            'listFormSortingFieldAppend' => $listFormSortingFieldAppend ?? Generator::UNDEFINED,
            'listFormFilterByGroups' => $listFormFilterByGroups ?? Generator::UNDEFINED,
            'listFormFilterByAppend' => $listFormFilterByAppend ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'value' => $this->combine($externalDocs, $attachables),
        ];

        switch ($tag) {
            case false:
                $properties['name'] = Generator::UNDEFINED;
                break;
            case true:
                $properties['name'] = $this->guessTag($class);
                break;
            default:
                $properties['name'] = $tag;
                break;
        }

        parent::__construct($properties);
    }

    public function guessSubject(string $class): string
    {
        $reflectionClass = new \ReflectionClass($class);

        return (string) u($reflectionClass->getShortName())->snake()->replace('_', ' ');
    }

    public function guessTag(string $class): string
    {
        $reflectionClass = new \ReflectionClass($class);

        return (string) u($reflectionClass->getShortName())->title();
    }

    public static function getContextResourceProperty(OAA\AbstractAnnotation $sourceAnnotation, string $propertyName)
    {
        foreach ($sourceAnnotation->_context->annotations as $annotation) {
            if ($annotation instanceof Resource) {
                return $annotation->{$propertyName};
            }
        }
    }
}
