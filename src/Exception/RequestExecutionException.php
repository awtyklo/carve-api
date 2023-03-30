<?php

namespace Carve\ApiBundle\Exception;

use Carve\ApiBundle\Enum\RequestExecutionExceptionSeverity;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class RequestExecutionException extends ConflictHttpException
{
    protected array $errors = [];

    protected $payload = null;

    public function __construct(?string $error = null, array $parameters = [], ?RequestExecutionExceptionSeverity $severity = RequestExecutionExceptionSeverity::ERROR, ?string $message = '', \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message, $previous, $code, $headers);

        if ($error) {
            $this->add($error, $parameters, $severity);
        }
    }

    public function getSeverity(): RequestExecutionExceptionSeverity
    {
        if (!$this->hasErrors()) {
            return RequestExecutionExceptionSeverity::ERROR;
        }

        foreach ($this->getErrors() as $error) {
            if (RequestExecutionExceptionSeverity::ERROR === $error['severity']) {
                return RequestExecutionExceptionSeverity::ERROR;
            }
        }

        return RequestExecutionExceptionSeverity::WARNING;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->getErrors()) > 0;
    }

    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    public function clear(): void
    {
        $this->errors = [];
    }

    public function addError(string $error, array $parameters = []): void
    {
        $this->add($error, $parameters, RequestExecutionExceptionSeverity::ERROR);
    }

    public function addWarning(string $error, array $parameters = []): void
    {
        $this->add($error, $parameters, RequestExecutionExceptionSeverity::WARNING);
    }

    public function merge(RequestExecutionException $exception, ?RequestExecutionExceptionSeverity $severity = null): void
    {
        foreach ($exception->getErrors() as $error) {
            $this->add($error['message'], $error['parameters'], $severity ?? $error['severity']);
        }
    }

    public function mergeAsWarnings(RequestExecutionException $exception): void
    {
        $this->merge($exception, RequestExecutionExceptionSeverity::WARNING);
    }

    public function mergeAsErrors(RequestExecutionException $exception): void
    {
        $this->merge($exception, RequestExecutionExceptionSeverity::ERROR);
    }

    public function add(string $error, array $parameters = [], ?RequestExecutionExceptionSeverity $severity = RequestExecutionExceptionSeverity::ERROR): void
    {
        $this->errors[] = [
            'message' => $error,
            'parameters' => $parameters,
            'severity' => $severity,
        ];
    }
}
