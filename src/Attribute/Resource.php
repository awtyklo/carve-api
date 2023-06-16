<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use Carve\ApiBundle\Form\BatchQueryType;
use Carve\ApiBundle\Form\ExportCsvQueryType;
use Carve\ApiBundle\Form\ExportExcelQueryType;
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
    public $batchFormClass = Generator::UNDEFINED;
    public $exportCsvFormClass = Generator::UNDEFINED;
    public $exportExcelFormClass = Generator::UNDEFINED;
    public $exportFormFieldGroups = Generator::UNDEFINED;
    public $exportFormFieldAppend = Generator::UNDEFINED;
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
        'batchFormClass' => 'string',
        'exportCsvFormClass' => 'string',
        'exportExcelFormClass' => 'string',
        'exportFormFieldGroups' => 'array',
        'exportFormFieldAppend' => 'array',
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
        ?string $batchFormClass = BatchQueryType::class,
        ?string $exportCsvFormClass = ExportCsvQueryType::class,
        ?string $exportExcelFormClass = ExportExcelQueryType::class,
        ?array $exportFormFieldGroups = null,
        ?array $exportFormFieldAppend = null,
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
            'batchFormClass' => $batchFormClass ?? Generator::UNDEFINED,
            'exportCsvFormClass' => $exportCsvFormClass ?? Generator::UNDEFINED,
            'exportExcelFormClass' => $exportExcelFormClass ?? Generator::UNDEFINED,
            'exportFormFieldGroups' => $exportFormFieldGroups ?? Generator::UNDEFINED,
            'exportFormFieldAppend' => $exportFormFieldAppend ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'value' => $this->combine($externalDocs, $attachables),
        ];

        // cannot use switch as it uses loose comparision
        if (false === $tag) {
            $properties['name'] = Generator::UNDEFINED;
        } elseif (true === $tag) {
            $properties['name'] = $this->guessTag($class);
        } else {
            $properties['name'] = $tag;
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
