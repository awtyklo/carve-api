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
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
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

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!($data instanceof FlattenException)) {
            return false;
        }

        return RequestExecutionException::class === $data->getClass();
    }

    public function getSupportedTypes(?string $format): array
    {
        // In Symfony 5.4 results where not cached by default. Adjust when needed.
        return [
            'object' => false,
            '*' => false,
        ];
    }
}
