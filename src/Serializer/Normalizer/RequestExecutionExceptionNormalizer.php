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

        $messages = $exception->getMessages();
        foreach ($messages as $key => $message) {
            $messages[$key]['parameters'] = MessageParameterNormalizer::normalize($message['parameters']);
        }

        return [
            'code' => $exception->getStatusCode(),
            'executionSeverity' => $exception->getSeverity(),
            'messages' => $messages,
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
