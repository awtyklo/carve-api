<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Trait;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Model\ExportExcelQueryInterface;
use Carve\ApiBundle\View\ExportExcelView;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait ApiExportExcelTrait
{
    #[Rest\Post('/export/excel')]
    #[Api\Summary('Export {{ subjectPluralLower }} as Excel')]
    #[Api\RequestBodyExportExcel]
    #[Api\Response200(description: 'Exported data as Excel', content: new OA\MediaType(mediaType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', schema: new OA\Schema(type: 'string', format: 'binary')))]
    #[Api\Response400]
    public function exportExcelAction(Request $request)
    {
        return $this->handleForm($this->getExportExcelFormClass(), $request, [$this, 'processExportExcel'], $this->getExportExcelObject(), $this->getExportExcelFormOptions());
    }

    protected function getExportExcelObject()
    {
        return null;
    }

    protected function getExportExcelFormOptions(): array
    {
        return $this->getDefaultExportExcelFormOptions();
    }

    protected function processExportExcel(ExportExcelQueryInterface $exportExcelQuery, FormInterface $form)
    {
        $queryBuilder = $this->getExportQueryBuilder($exportExcelQuery);
        $results = $queryBuilder->getQuery()->getResult();

        return new ExportExcelView($results, $exportExcelQuery->getFields(), $exportExcelQuery->getFilename(), $exportExcelQuery->getSheetName());
    }
}
