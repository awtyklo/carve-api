<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Serializer\Normalizer;

use Carve\ApiBundle\Helper\MessageParameterNormalizer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Overrides default FormErrorNormalizer to additionally pass parameters from error messages.
 */
class FormErrorNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return [
            'code' => isset($context['status_code']) ? $context['status_code'] : null,
            'message' => 'validation.failed',
            'errors' => $this->convertFormToArray($object),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof FormInterface && $data->isSubmitted() && !$data->isValid();
    }

    private function convertFormToArray(FormInterface $data): array
    {
        $form = $errors = [];

        foreach ($data->getErrors() as $error) {
            $errors[] = [
                'message' => $error->getMessage(),
                'parameters' => MessageParameterNormalizer::normalize($error->getMessageParameters()),
            ];
        }

        if ($errors) {
            $form['errors'] = $errors;
        }

        $children = [];
        foreach ($data->all() as $child) {
            if ($child instanceof FormInterface) {
                $children[$child->getName()] = $this->convertFormToArray($child);
            }
        }

        if ($children) {
            $form['children'] = $children;
        }

        return $form;
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
