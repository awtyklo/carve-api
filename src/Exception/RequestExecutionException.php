<?php

namespace Carve\ApiBundle\Exception;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class RequestExecutionException extends ConflictHttpException
{
    private $errors = [];

    /**
     * @param string|null     $message  The internal exception message
     * @param \Throwable|null $previous The previous exception
     * @param int             $code     The internal exception code
     */
    public function __construct(?string $errorMessage = null, array $errorParameters = [], string $message = 'error.requestExecutionFailed', \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message, $previous, $code, $headers);

        if ($errorMessage) {
            $this->addError($errorMessage, $errorParameters);
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }

    public function addError(string $message, array $parameters = []): void
    {
        $this->errors[] = [
            'message' => $message,
            'parameters' => $parameters,
        ];
    }
}
