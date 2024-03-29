<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Trait;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Model\ExportCsvQueryInterface;
use Carve\ApiBundle\View\ExportCsvView;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait ApiExportCsvTrait
{
    #[Rest\Post('/export/csv')]
    #[Api\Summary('Export {{ subjectPluralLower }} as CSV')]
    #[Api\RequestBodyExportCsv]
    #[Api\Response200(description: 'Exported data as CSV', content: new OA\MediaType(mediaType: 'text/csv', schema: new OA\Schema(type: 'string')))]
    #[Api\Response400]
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

        return new ExportCsvView($results, $exportCsvQuery->getFields(), $exportCsvQuery->getFilename());
    }
}
