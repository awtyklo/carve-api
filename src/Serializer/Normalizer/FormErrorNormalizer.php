<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Serializer\Normalizer;

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
    public function normalize($object, $format = null, array $context = []): array
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
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof FormInterface && $data->isSubmitted() && !$data->isValid();
    }

    private function convertFormToArray(FormInterface $data): array
    {
        $form = $errors = [];

        foreach ($data->getErrors() as $error) {
            $errorPayload = [
                'message' => $error->getMessage(),
                'parameters' => [],
            ];

            foreach ($error->getMessageParameters() as $messageParameter => $messageParameterValue) {
                $errorPayload['parameters'][$this->normalizeMessageParameter($messageParameter)] = $messageParameterValue;
            }

            $errors[] = $errorPayload;
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
