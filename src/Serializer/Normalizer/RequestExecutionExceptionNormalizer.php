<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Serializer\Normalizer;

use Carve\ApiBundle\Exception\RequestExecutionException;
use Carve\ApiBundle\Helper\MessageParameterNormalizer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Provides normalization for Carve\ApiBundle\Exception\RequestExecutionException.
 */
class RequestExecutionExceptionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = []): array
    {
        $exception = $context['exception'] ?? null;
        if (!$exception || !($exception instanceof RequestExecutionException)) {
            return [];
        }

        $errors = $exception->getErrors();
        foreach ($errors as $key => $error) {
            $errors[$key]['parameters'] = MessageParameterNormalizer::normalize($error['parameters']);
        }

        return [
            'code' => $exception->getStatusCode(),
            'severity' => $exception->getSeverity(),
            'errors' => $errors,
            'payload' => $exception->getPayload(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!($data instanceof FlattenException)) {
            return false;
        }

        return RequestExecutionException::class === $data->getClass();
    }
}
