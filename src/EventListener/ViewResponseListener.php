<?php

namespace Carve\ApiBundle\EventListener;

use Carve\ApiBundle\Serializer\Normalizer\ExportEnumNormalizer;
use Carve\ApiBundle\Service\Helper\RoleBasedSerializerGroupsManagerTrait;
use Carve\ApiBundle\View\ExportCsvView;
use Carve\ApiBundle\View\ExportExcelView;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\FOSRestBundle;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This is adjusted copy of FOS\RestBundle\EventListener\ViewResponseListener.
 * It additionally handles Carve\ApiBundle\View\ExportCsvView and Carve\ApiBundle\View\ExportExcelView.
 */
class ViewResponseListener implements EventSubscriberInterface
{
    use RoleBasedSerializerGroupsManagerTrait;

    private $viewHandler;
    private $forceView;

    public function __construct(ViewHandlerInterface $viewHandler, bool $forceView = true)
    {
        $this->viewHandler = $viewHandler;
        $this->forceView = $forceView;
    }

    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->attributes->get(FOSRestBundle::ZONE_ATTRIBUTE, true)) {
            return;
        }
        $configuration = $request->attributes->get('_template');

        $view = $event->getControllerResult();
        $exportView = null;
        if ($view instanceof ExportCsvView || $view instanceof ExportExcelView) {
            $exportView = $view;
            // Redirect results to be handled as designed in FosRestBundle by ViewResponseListener
            $view = $view->getResults();
        }

        if (!$view instanceof View) {
            if (!$configuration instanceof ViewAnnotation && !$this->forceView) {
                return;
            }

            $view = new View($view);
        }

        if ($configuration instanceof ViewAnnotation) {
            if (null !== $configuration->getStatusCode() && (null === $view->getStatusCode() || Response::HTTP_OK === $view->getStatusCode())) {
                $view->setStatusCode($configuration->getStatusCode());
            }
            $context = $view->getContext();
            if ($configuration->getSerializerGroups()) {
                if (null === $context->getGroups()) {
                    $context->setGroups($configuration->getSerializerGroups());
                } else {
                    $context->setGroups(array_merge($context->getGroups(), $configuration->getSerializerGroups()));
                }
            }

            $context->setGroups(array_unique(array_merge($context->getGroups(), $this->getRoleBasedSerializerGroupsByOwner($configuration->getOwner()))));

            if (true === $configuration->getSerializerEnableMaxDepthChecks()) {
                $context->enableMaxDepth();
            } elseif (false === $configuration->getSerializerEnableMaxDepthChecks()) {
                $context->disableMaxDepth();
            }
        }

        if (null === $view->getFormat()) {
            $view->setFormat($request->getRequestFormat());
        }

        if (null !== $exportView) {
            // Force json format when handling export view
            $view->setFormat('json');
            // Extend groups with a custom 'special:export' group when handling export view
            // TODO does $context variable exist if if ($configuration instanceof ViewAnnotation) { is false???
            $context->setGroups(array_merge($context->getGroups(), [ExportEnumNormalizer::EXPORT_GROUP]));
        }

        $response = $this->viewHandler->handle($view, $request);

        if (null === $exportView) {
            // Default processing
            $event->setResponse($response);

            return;
        }

        // Serialized results from default processing
        $results = json_decode($response->getContent(), true);
        $results = $this->filterExportResults($exportView->getFields(), $results);
        $results = $this->normalizeExportResults($results);
        array_unshift($results, $this->getExportLabels($exportView->getFields()));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($results, null, 'A1', true);
        if ($exportView instanceof ExportExcelView) {
            $sheet->setTitle($exportView->getSheetName());
        }

        $filename = $exportView->getFilename();
        $exportFilename = tempnam(sys_get_temp_dir(), $filename);

        $writer = $this->getExportWriter($exportView, $spreadsheet);
        $writer->save($exportFilename);

        $fileResponse = new BinaryFileResponse($exportFilename);
        $fileResponse->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        $event->setResponse($fileResponse);
    }

    protected function getExportWriter($exportView, Spreadsheet $spreadsheet)
    {
        switch (true) {
            case $exportView instanceof ExportExcelView:
                return new Xlsx($spreadsheet);
            case $exportView instanceof ExportCsvView:
                return new Csv($spreadsheet);
        }

        throw new \Exception('Unsupported $exportView of class \"'.get_class($exportView).'\"');
    }

    /**
     * Filters results based in $fields (array of Carve\ApiBundle\Model\ExportQueryField).
     */
    protected function filterExportResults(array $fields, array $results): array
    {
        $fieldNames = array_map(function ($field) {
            return $field->getField();
        }, $fields);

        return array_map(function ($result) use ($fieldNames) {
            $exportResult = [];
            // In case of optimizing remember that $result and $fieldNames may have keys in different order
            foreach ($fieldNames as $fieldName) {
                $exportResult[$fieldName] = $result[$fieldName] ?? null;
            }

            return $exportResult;
        }, $results);
    }

    /**
     * Normalizes results to ensure that each result is one dimensional array.
     */
    protected function normalizeExportResults(array $results): array
    {
        return array_map(function ($result) {
            return array_map(function ($value) {
                if (isset($value['representation'])) {
                    return $value['representation'];
                }

                return $value;
            }, $result);
        }, $results);
    }

    /**
     * Returns labels based on $fields (array of Carve\ApiBundle\Model\ExportQueryField).
     */
    protected function getExportLabels(array $fields): array
    {
        return array_map(function ($field) {
            return $field->getLabel();
        }, $fields);
    }

    public static function getSubscribedEvents(): array
    {
        // Must be executed before SensioFrameworkExtraBundle's listener
        return [
            KernelEvents::VIEW => ['onKernelView', 30],
        ];
    }
}
