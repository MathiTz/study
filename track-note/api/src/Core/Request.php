<?php

declare(strict_types=1);

namespace TrackNote\Core;

class Request
{
    private string $method;
    private string $uri;
    private string $path;
    private array $query;
    private array $body;
    private array $headers;
    private array $params = [];

    public function __construct()
    {
        $this->method = strtoupper($_SERVER["REQUEST_METHOD"] ?? "GET");
        $this->uri = $_SERVER["REQUEST_URI"] ?? "/";
        $this->path = parse_url($this->uri, PHP_URL_PATH) ?? "/";
        $this->query = $_GET;
        $this->headers = $this->parseHeaders();
        $this->body = $this->parseBody();
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, "HTTP_")) {
                $header = str_replace("_", "-", substr($key, 5));
                $headers[strtolower($header)] = $value;
            }
        }

        if (isset($_SERVER["CONTENT_TYPE"])) {
            $headers["content-type"] = $_SERVER["CONTENT_TYPE"];
        }

        return $headers;
    }

    private function parseBody(): array
    {
        if ($this->method === "GET") {
            return [];
        }

        $contentType = $this->headers["content-type"] ?? "";

        if (str_contains($contentType, "application/json")) {
            $rawBody = file_get_contents("php://input");
            if (!empty($rawBody)) {
                $decoded = json_decode($rawBody, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
            return [];
        }

        if (
            str_contains($contentType, "application/x-www-form-urlencoded") ||
            str_contains($contentType, "multipart/form-data")
        ) {
            return $_POST;
        }

        return [];
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function body(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->body;
        }
        return $this->body[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? ($this->query[$key] ?? $default);
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function isJson(): bool
    {
        return str_contains(
            $this->header("content-type", ""),
            "application/json",
        );
    }

    public function expectsJson(): bool
    {
        return str_contains($this->header("accept", ""), "application/json");
    }

    public function ip(): string
    {
        return $_SERVER["HTTP_X_FORWARDED_FOR"] ??
            ($_SERVER["HTTP_X_REAL_IP"] ??
                ($_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"));
    }

    public function userAgent(): string
    {
        return $_SERVER["HTTP_USER_AGENT"] ?? "";
    }

    public function has(string $key): bool
    {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }

    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $result[$key] = $this->input($key);
            }
        }
        return $result;
    }

    public function validate(array $rules): array
    {
        $errors = [];
        $data = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $this->input($field);
            $ruleList = is_string($fieldRules)
                ? explode("|", $fieldRules)
                : $fieldRules;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ":")) {
                    [$rule, $paramStr] = explode(":", $rule, 2);
                    $params = explode(",", $paramStr);
                }

                $error = $this->validateRule($field, $value, $rule, $params);
                if ($error !== null) {
                    $errors[$field][] = $error;
                }
            }

            if (!isset($errors[$field])) {
                $data[$field] = $value;
            }
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        return $data;
    }

    private function validateRule(
        string $field,
        mixed $value,
        string $rule,
        array $params,
    ): ?string {
        return match ($rule) {
            "required" => $value === null || $value === ""
                ? "$field is required"
                : null,
            "string" => $value !== null && !is_string($value)
                ? "$field must be a string"
                : null,
            "email" => $value !== null &&
            !filter_var($value, FILTER_VALIDATE_EMAIL)
                ? "$field must be a valid email"
                : null,
            "min" => $value !== null &&
            strlen((string) $value) < (int) $params[0]
                ? "$field must be at least {$params[0]} characters"
                : null,
            "max" => $value !== null &&
            strlen((string) $value) > (int) $params[0]
                ? "$field must not exceed {$params[0]} characters"
                : null,
            "uuid" => $value !== null &&
            !preg_match(
                '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
                $value,
            )
                ? "$field must be a valid UUID"
                : null,
            "datetime" => $value !== null && !$this->isValidDatetime($value)
                ? "$field must be a valid ISO 8601 datetime"
                : null,
            "integer" => $value !== null &&
            !filter_var($value, FILTER_VALIDATE_INT)
                ? "$field must be an integer"
                : null,
            "boolean" => $value !== null &&
            !is_bool($value) &&
            !in_array($value, ["0", "1", 0, 1, "true", "false"], true)
                ? "$field must be a boolean"
                : null,
            "nullable" => null,
            default => null,
        };
    }

    private function isValidDatetime(string $value): bool
    {
        // Try standard ISO 8601 format (ATOM)
        if (
            \DateTimeImmutable::createFromFormat(
                \DateTimeInterface::ATOM,
                $value,
            ) !== false
        ) {
            return true;
        }

        // Try ISO 8601 with milliseconds (JavaScript format)
        if (
            \DateTimeImmutable::createFromFormat("Y-m-d\TH:i:s.vP", $value) !==
            false
        ) {
            return true;
        }

        // Try ISO 8601 with milliseconds and Z timezone
        if (
            \DateTimeImmutable::createFromFormat("Y-m-d\TH:i:s.v\Z", $value) !==
            false
        ) {
            return true;
        }

        return false;
    }
}
