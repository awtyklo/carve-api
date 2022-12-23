<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Controller;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Enum\ListQueryFilterType;
use Carve\ApiBundle\Model\ExportQueryInterface;
use Carve\ApiBundle\Model\ListQueryFilterInterface;
use Carve\ApiBundle\Model\ListQueryInterface;
use Carve\ApiBundle\Model\ListQuerySortingInterface;
use Carve\ApiBundle\Service\Helper\DenyManagerTrait;
use Carve\ApiBundle\Service\Helper\EntityManagerTrait;
use Carve\ApiBundle\Service\Helper\SerializerExtractorTrait;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Could not find option to ignore all properties by default in symfony/serializer
// By default set an invalid group to avoid exposing contents of whole entity
#[Rest\View(serializerGroups: ['__invalid__'])]
abstract class AbstractApiController extends AbstractFOSRestController
{
    use EntityManagerTrait;
    use DenyManagerTrait;
    use SerializerExtractorTrait;

    protected function modifyQueryBuilder(QueryBuilder $queryBuilder, string $alias): void
    {
        // Empty method for customization
    }

    protected function modifySorting(ListQuerySortingInterface $sorting, QueryBuilder $queryBuilder, string $alias): bool
    {
        // Empty method for customization
        return false;
    }

    protected function modifyFilter(ListQueryFilterInterface $filter, QueryBuilder $queryBuilder, string $alias): bool
    {
        // Empty method for customization
        return false;
    }

    protected function find(int $id, ?string $denyKey = null, callable $modifyQueryBuilder = null, string $alias = 'o'): object
    {
        $queryBuilder = $this->getQueryBuilder($modifyQueryBuilder, $alias);

        $queryBuilder->andWhere($alias.'.id = :id');
        $queryBuilder->setParameter('id', $id);
        $queryBuilder->setMaxResults(1);

        $object = $queryBuilder->getQuery()->getOneOrNullResult();

        if (!$object) {
            throw new NotFoundHttpException();
        }

        if (null !== $denyKey && $this->hasDenyClass()) {
            $denyClass = $this->getDenyClass();
            if ($this->isDenied($denyClass, $denyKey, $object)) {
                throw new AccessDeniedHttpException();
            }

            $this->fillDeny($denyClass, $object);
        }

        return $object;
    }

    // TODO ListQueryInterface $listQuery is not a valid interface here. Rethink and fix after batch actions are ready
    protected function getListQueryBuilder(ListQueryInterface $listQuery, callable $modifyQueryBuilder = null, string $alias = 'o'): QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder($modifyQueryBuilder, $alias);
        $queryBuilder->distinct();

        $this->applyListQuerySorting($listQuery->getSorting(), $queryBuilder, $alias);
        $this->applyListQueryFilters($listQuery->getFilters(), $queryBuilder, $alias);

        // TODO Left for reference to work with selected (batch actions)
        // if (count($selected) > 0) {
        //     $this->applySelected($queryBuilder, $alias, $selected);
        // } else {
        //     $this->applyFilters($queryBuilder, $alias, $filters, $class);
        // }

        $page = $listQuery->getPage() ?? 1;
        $rowsPerPage = $listQuery->getRowsPerPage() ?? 10;
        $queryBuilder->setFirstResult(($page - 1) * $rowsPerPage);
        $queryBuilder->setMaxResults($rowsPerPage);

        return $queryBuilder;
    }

    protected function getExportQueryBuilder(ExportQueryInterface $exportQuery, callable $modifyQueryBuilder = null, string $alias = 'o'): QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder($modifyQueryBuilder, $alias);
        $queryBuilder->distinct();

        $this->applyListQuerySorting($exportQuery->getSorting(), $queryBuilder, $alias);
        $this->applyListQueryFilters($exportQuery->getFilters(), $queryBuilder, $alias);

        return $queryBuilder;
    }

    protected function applyListQuerySorting(array $listQuerySorting, QueryBuilder $queryBuilder, string $alias): void
    {
        foreach ($listQuerySorting as $sorting) {
            $skip = $this->modifySorting($sorting, $queryBuilder, $alias);
            if (false === $skip) {
                $queryBuilder->addOrderBy($alias.'.'.$sorting->getField(), $sorting->getDirection()->value);
            }
        }
    }

    protected function applyListQueryFilters(array $listQueryFilters, QueryBuilder $queryBuilder, string $alias): void
    {
        $leftJoinAliases = [];

        foreach ($listQueryFilters as $key => $filter) {
            $skip = $this->modifyFilter($filter, $queryBuilder, $alias);
            if (false === $skip) {
                $filterValue = $filter->getFilterValue();

                $filterParameter = str_replace('.', '_', $filter->getFilterBy().$key);
                [$filterBy, $filterAlias] = $this->processFilterLeftJoin($filter, $queryBuilder, $alias, $leftJoinAliases);

                $this->processFilterValue($filter, $filterValue);

                $this->applyFilter($queryBuilder, $filter->getFilterType(), $filterAlias, $filterBy, $filterParameter, $filterValue);
            }
        }
    }

    protected function processFilterValue(ListQueryFilterInterface $filter, $filterValue): void
    {
        $filterType = $filter->getFilterType();

        if (in_array($filterType, [ListQueryFilterType::DATEGREATERTHAN,  ListQueryFilterType::DATEGREATERTHANOREQUAL])) {
            $filterValue->setTime(0, 0, 0);
        }

        if (in_array($filterType, [ListQueryFilterType::DATELESSTHAN,  ListQueryFilterType::DATELESSTHANOREQUAL])) {
            $filterValue->setTime(23, 59, 59);
        }

        if (in_array($filterType, [ListQueryFilterType::DATETIMEGREATERTHAN,  ListQueryFilterType::DATETIMEGREATERTHANOREQUAL, ListQueryFilterType::TIMEGREATERTHANOREQUAL, ListQueryFilterType::TIMEGREATERTHANOREQUAL])) {
            $filterValue->setTime((int) $filterValue->format('H'), (int) $filterValue->format('i'), 0);
        }

        if (in_array($filterType, [ListQueryFilterType::DATETIMELESSTHAN,  ListQueryFilterType::DATETIMELESSTHANOREQUAL, ListQueryFilterType::TIMELESSTHAN,  ListQueryFilterType::TIMELESSTHANOREQUAL])) {
            $filterValue->setTime((int) $filterValue->format('H'), (int) $filterValue->format('i'), 59);
        }

        if (in_array($filter->getFilterType(), [ListQueryFilterType::TIMEGREATERTHANOREQUAL, ListQueryFilterType::TIMEGREATERTHANOREQUAL, ListQueryFilterType::TIMELESSTHAN,  ListQueryFilterType::TIMELESSTHANOREQUAL])) {
            $filterValue = $filterValue->format('H:i:s');
        }
    }

    protected function processFilterLeftJoin(ListQueryFilterInterface $filter, QueryBuilder $queryBuilder, string $alias, array $leftJoinAliases): array
    {
        $filterByExploded = explode('.', $filter->getFilterBy());

        if (count($filterByExploded) > 1) {
            $leftJoinAlias = 'leftJoin_'.$filterByExploded[0];

            if (!in_array($leftJoinAlias, $leftJoinAliases)) {
                $queryBuilder->leftJoin($alias.'.'.$filterByExploded[0], $leftJoinAlias);
                $leftJoinAliases[] = $leftJoinAlias;
            }

            return [$filterByExploded[1], $leftJoinAlias];
        }

        return [$filter->getFilterBy(), $alias];
    }

    protected function applyFilter(QueryBuilder $queryBuilder, ListQueryFilterType $filterType, string $filterAlias, string $filterBy, string $filterParameter, $filterValue): void
    {
        switch ($filterType) {
            case ListQueryFilterType::BOOLEAN:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' = :'.$filterParameter);
                $queryBuilder->setParameter($filterParameter, $filterValue);
                break;
            case ListQueryFilterType::EQUAL:
                if ($this->isManyToManyRelationship($filterBy)) {
                    $queryBuilder->andWhere(':'.$filterParameter.' MEMBER OF '.$filterAlias.'.'.$filterBy);
                    $queryBuilder->setParameter($filterParameter, $filterValue);
                } else {
                    $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' = :'.$filterParameter);
                    $queryBuilder->setParameter($filterParameter, $filterValue);
                }
                break;
            case ListQueryFilterType::EQUALMULTIPLE:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' IN (:'.$filterParameter.')');
                $queryBuilder->setParameter($filterParameter, $filterValue);
                break;
            case ListQueryFilterType::LIKE:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' LIKE :'.$filterParameter);
                $queryBuilder->setParameter($filterParameter, '%'.$filterValue.'%');
                break;
            case ListQueryFilterType::STARTSWITH:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' LIKE :'.$filterParameter);
                $queryBuilder->setParameter($filterParameter, $filterValue.'%');
                break;
            case ListQueryFilterType::ENDSWITH:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' LIKE :'.$filterParameter);
                $queryBuilder->setParameter($filterParameter, '%'.$filterValue);
                break;
            case ListQueryFilterType::GREATERTHAN:
            case ListQueryFilterType::DATEGREATERTHAN:
            case ListQueryFilterType::DATETIMEGREATERTHAN:
            case ListQueryFilterType::TIMEGREATERTHAN:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' > :'.$filterParameter);
                $queryBuilder->setParameter($filterParameter, $filterValue);
                break;
            case ListQueryFilterType::GREATERTHANOREQUAL:
            case ListQueryFilterType::DATEGREATERTHANOREQUAL:
            case ListQueryFilterType::DATETIMEGREATERTHANOREQUAL:
            case ListQueryFilterType::TIMEGREATERTHANOREQUAL:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' >= :'.$filterParameter);
                $queryBuilder->setParameter($filterParameter, $filterValue);
                break;
            case ListQueryFilterType::LESSTHAN:
            case ListQueryFilterType::DATELESSTHAN:
            case ListQueryFilterType::DATETIMELESSTHAN:
            case ListQueryFilterType::TIMELESSTHAN:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' < :'.$filterParameter);
                $queryBuilder->setParameter($filterParameter, $filterValue);
                break;
            case ListQueryFilterType::LESSTHANOREQUAL:
            case ListQueryFilterType::DATELESSTHANOREQUAL:
            case ListQueryFilterType::DATETIMELESSTHANOREQUAL:
            case ListQueryFilterType::TIMELESSTHANOREQUAL:
                $queryBuilder->andWhere($filterAlias.'.'.$filterBy.' <= :'.$filterParameter);
                $queryBuilder->setParameter($filterParameter, $filterValue);
                break;
        }
    }

    protected function getQueryBuilder(callable $modifyQueryBuilder = null, string $alias = 'o'): QueryBuilder
    {
        $queryBuilder = $this->getRepository($this->getClass())->createQueryBuilder($alias);

        if (null !== $modifyQueryBuilder) {
            $modifyQueryBuilder($queryBuilder, $alias);
        } else {
            $this->modifyQueryBuilder($queryBuilder, $alias);
        }

        return $queryBuilder;
    }

    protected function handleForm(string $formClass, Request $request, callable $callback, $object = null, array $options = [])
    {
        $form = $this->createForm($formClass, $object, $options);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $object = $form->getData();

            return $callback($object, $form);
        }

        return $form;
    }

    protected function getClass(): string
    {
        return $this->getApiResourceAttributeArgument('class');
    }

    protected function getCreateFormClass(): string
    {
        $createFormClass = $this->getApiResourceAttributeArgument('createFormClass');
        if (null === $createFormClass) {
            throw new \Exception('Argument "createFormClass" not defined. Please define it in "Api\Resource" attribute');
        }

        return $createFormClass;
    }

    protected function getEditFormClass(): string
    {
        $editFormClass = $this->getApiResourceAttributeArgument('editFormClass');
        if (null === $editFormClass) {
            throw new \Exception('Argument "editFormClass" not defined. Please define it in "Api\Resource" attribute');
        }

        return $editFormClass;
    }

    protected function getListFormClass(): string
    {
        $listFormClass = $this->getApiResourceAttributeArgument('listFormClass');
        if (null === $listFormClass) {
            throw new \Exception('Argument "listFormClass" not defined. Please define it in "Api\Resource" attribute');
        }

        return $listFormClass;
    }

    protected function getExportCsvFormClass(): string
    {
        $exportCsvFormClass = $this->getApiResourceAttributeArgument('exportCsvFormClass');
        if (null === $exportCsvFormClass) {
            throw new \Exception('Argument "exportCsvFormClass" not defined. Please define it in "Api\Resource" attribute');
        }

        return $exportCsvFormClass;
    }

    protected function getExportExcelFormClass(): string
    {
        $exportExcelFormClass = $this->getApiResourceAttributeArgument('exportExcelFormClass');
        if (null === $exportExcelFormClass) {
            throw new \Exception('Argument "exportExcelFormClass" not defined. Please define it in "Api\Resource" attribute');
        }

        return $exportExcelFormClass;
    }

    protected function hasDenyClass(): bool
    {
        if (null === $this->getDenyClass()) {
            return false;
        }

        if (Generator::UNDEFINED === $this->getDenyClass()) {
            return false;
        }

        return true;
    }

    protected function getDenyClass(): ?string
    {
        return $this->getApiResourceAttributeArgument('denyClass');
    }

    protected function getApiResourceAttributeArgument(string $argument)
    {
        $attribute = $this->getApiResourceAttribute();
        $attributeInstance = $attribute->newInstance();

        if ($attributeInstance->$argument === Generator::UNDEFINED) {
            return null;
        }

        return $attributeInstance->$argument;
    }

    protected function getApiResourceAttribute()
    {
        $reflectionClass = new \ReflectionClass($this);
        $attributes = $reflectionClass->getAttributes();

        foreach ($attributes as $attribute) {
            if (Api\Resource::class === $attribute->getName()) {
                return $attribute;
            }
        }

        throw new \Exception('Defining "Carve\ApiBundle\Attribute\Resource" attribute is required when extending AbstractApiController');
    }

    protected function isManyToManyRelationship(string $field)
    {
        $classMetadataFactory = $this->entityManager->getMetadataFactory();
        $metadataClass = $classMetadataFactory->getMetadataFor($this->getClass());

        if (isset($metadataClass->associationMappings[$field])) {
            if (ClassMetadataInfo::MANY_TO_MANY == $metadataClass->associationMappings[$field]['type']) {
                return true;
            }
        }

        return false;
    }

    protected function getSerializerGroups(): ?array
    {
        $reflectionClass = new \ReflectionClass($this);
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

    protected function getSortingFieldChoices(): array
    {
        $class = $this->getClass();
        $defaultSerializerGroups = $this->getSerializerGroups();

        $sortingSerializerGroups = $this->getApiResourceAttributeArgument('listFormSortingFieldGroups');
        if (null === $sortingSerializerGroups) {
            if (null !== $defaultSerializerGroups) {
                $sortingSerializerGroups = AbstractApiController::normalizeDefaultSerializerGroups($defaultSerializerGroups);
            }
        }

        $sortingFieldChoices = $this->serializerExtractor->getProperties($class, ['serializer_groups' => $sortingSerializerGroups]);
        $sortingFieldAppend = $this->getApiResourceAttributeArgument('listFormSortingFieldAppend');
        if (null !== $sortingFieldAppend) {
            $sortingFieldChoices = AbstractApiController::appendFieldChoice($sortingFieldChoices, $sortingFieldAppend);
        }

        return $sortingFieldChoices;
    }

    protected function getFilterFilterByChoices(): array
    {
        $class = $this->getClass();
        $defaultSerializerGroups = $this->getSerializerGroups();

        $filterBySerializerGroups = $this->getApiResourceAttributeArgument('listFormFilterByGroups');
        if (null === $filterBySerializerGroups) {
            if (null !== $defaultSerializerGroups) {
                $filterBySerializerGroups = AbstractApiController::normalizeDefaultSerializerGroups($defaultSerializerGroups);
            }
        }

        $filterByChoices = $this->serializerExtractor->getProperties($class, ['serializer_groups' => $filterBySerializerGroups]);
        $filterByAppend = $this->getApiResourceAttributeArgument('listFormFilterByAppend');
        if (null !== $filterByAppend) {
            $filterByChoices = AbstractApiController::appendFieldChoice($filterByChoices, $filterByAppend);
        }

        return $filterByChoices;
    }

    protected function getFieldsFieldChoices(): array
    {
        $class = $this->getClass();
        $defaultSerializerGroups = $this->getSerializerGroups();

        $fieldSerializerGroups = $this->getApiResourceAttributeArgument('exportFormFieldGroups');
        if (null === $fieldSerializerGroups) {
            if (null !== $defaultSerializerGroups) {
                // We do not need to apply normalizeDefaultSerializerGroups here
                $fieldSerializerGroups = $defaultSerializerGroups;
            }
        }

        $fieldsFieldChoices = $this->serializerExtractor->getProperties($class, ['serializer_groups' => $fieldSerializerGroups]);
        $fieldAppendChoices = $this->getApiResourceAttributeArgument('exportFormFieldAppend');
        if (null !== $fieldAppendChoices) {
            $fieldsFieldChoices = AbstractApiController::appendFieldChoice($fieldsFieldChoices, $fieldAppendChoices);
        }

        return $fieldsFieldChoices;
    }

    protected function getDefaultListFormOptions(): array
    {
        return [
            'sorting_field_choices' => $this->getSortingFieldChoices(),
            'filter_filterBy_choices' => $this->getFilterFilterByChoices(),
        ];
    }

    protected function getDefaultExportCsvFormOptions(): array
    {
        return [
            'sorting_field_choices' => $this->getSortingFieldChoices(),
            'filter_filterBy_choices' => $this->getFilterFilterByChoices(),
            'fields_field_choices' => $this->getFieldsFieldChoices(),
        ];
    }

    protected function getDefaultExportExcelFormOptions(): array
    {
        return [
            'sorting_field_choices' => $this->getSortingFieldChoices(),
            'filter_filterBy_choices' => $this->getFilterFilterByChoices(),
            'fields_field_choices' => $this->getFieldsFieldChoices(),
        ];
    }

    public static function normalizeDefaultSerializerGroups(array $serializerGroups): array
    {
        // Replace serializer group 'identification' with 'id', when using default serializer groups
        $identificationKey = array_search('identification', $serializerGroups);
        if (false !== $identificationKey) {
            array_splice($serializerGroups, $identificationKey, 1, ['id']);
        }

        // Remove serializer group 'representation', when using default serializer groups
        $representationKey = array_search('representation', $serializerGroups);
        if (false !== $representationKey) {
            array_splice($serializerGroups, $representationKey, 1);
        }

        return $serializerGroups;
    }

    public static function appendFieldChoice(array $fieldChoices, array $fieldChoicesAppend): array
    {
        return array_unique(array_merge($fieldChoices, $fieldChoicesAppend));
    }
}
