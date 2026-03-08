<?php

declare(strict_types=1);

namespace TrackNote\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private mixed $body = null;

    public function __construct(mixed $body = null, int $statusCode = 200)
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers['Content-Type'] = 'application/json';
    }

    public static function json(mixed $data, int $statusCode = 200): self
    {
        return new self($data, $statusCode);
    }

    public static function success(mixed $data = null, string $message = 'Success'): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function created(mixed $data = null, string $message = 'Created'): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    public static function noContent(): self
    {
        return new self(null, 204);
    }

    public static function error(string $message, int $statusCode = 400, mixed $errors = null): self
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return new self($response, $statusCode);
    }

    public static function notFound(string $message = 'Resource not found'): self
    {
        return self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }

    public static function validationError(array $errors): self
    {
        return self::error('Validation failed', 422, $errors);
    }

    public static function serverError(string $message = 'Internal server error'): self
    {
        return self::error($message, 500);
    }

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        if ($this->body !== null && $this->statusCode !== 204) {
            echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }
}
