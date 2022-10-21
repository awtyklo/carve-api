<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Trait;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Model\ExportCsvQueryInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait ApiExportCsvTrait
{
    #[Rest\Post('/export/csv')]
    // TODO Documentation
    // #[Api\ListDescription]
    // #[Api\ListRequestBody]
    // #[Api\ListResponse200]
    // #[Api\Response400]
    public function exportCsvAction(Request $request)
    {
        return $this->handleForm($this->getExportCsvFormClass(), $request, [$this, 'processExportCsv'], $this->getExportCsvObject(), $this->getExportCsvFormOptions());
    }

    protected function getExportCsvObject()
    {
        return null;
    }

    protected function getExportCsvFormOptions(): array
    {
        return $this->getDefaultExportCsvFormOptions();
    }

    protected function processExportCsv(ExportCsvQueryInterface $exportCsvQuery, FormInterface $form)
    {
        $queryBuilder = $this->getExportQueryBuilder($exportCsvQuery);
        $results = $queryBuilder->getQuery()->getResult();

        dump($exportCsvQuery->getFields());
        // TODO Change it to maybye array_map
        $labels = array_column($exportCsvQuery->getFields(), 'label');
        $fields = array_column($exportCsvQuery->getFields(), 'field');

        dump($labels);
        dump($fields);

        dump($results);

        // TODO Build array for CSV based on $results
        // TODO Figure out a way to handle relations (i.e. manytoone)
        // TODO Figure out a way to handle enums
        // TODO Figure out a way to handle custom cases (override)

        // TODO Generate CSV

        // TODO Return file with $this->file (probably)
        return 'hello';
    }
}
