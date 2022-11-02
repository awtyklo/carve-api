<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Serializer\Normalizer;

use Carve\ApiBundle\Exception\RequestExecutionException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Provides normalization for Request Execution Exception.
 */
class RequestExecutionExceptionNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $returnArray = [
            'code' => $object->getStatusCode(),
            'message' => $object->getMessage(),
        ];

        if (!isset($context['exception'])) {
            return $returnArray;
        }
        if (!$context['exception'] instanceof RequestExecutionException) {
            return $returnArray;
        }

        $requestExecutionException = $context['exception'];

        $errorsArray = [];
        foreach ($requestExecutionException->getErrors() as $error) {
            $errorArray = [];
            if (isset($error['message'])) {
                $errorArray['message'] = $error['message'];
                $errorArray['parameters'] = [];
                if (isset($error['parameters'])) {
                    foreach ($error['parameters'] as $parameterName => $parameterValue) {
                        $errorArray['parameters'][$this->normalizeMessageParameter($parameterName)] = $parameterValue;
                    }
                }
            }
            $errorsArray[] = $errorArray;
        }

        $returnArray['errors'] = $errorsArray;

        return $returnArray;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof FlattenException) {
            return false;
        }

        return RequestExecutionException::class == $data->getClass();
    }

    /**
     * Every message parameter from default constraints (i.e. Symfony\Component\Validator\Constraints\GreaterThan)
     * are put in double brackets with spaces (to avoid accidental replacement when applying parameters).
     * We want to remove the double brackets and send them as it is and let parameter replacement be handled as needed by other application.
     */
    protected function normalizeMessageParameter($messageParameter)
    {
        return str_replace(['{{ ', ' }}'], '', $messageParameter);
    }
}
