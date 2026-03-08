<?php

declare(strict_types=1);

namespace TrackNote\Middleware;

use TrackNote\Core\Request;
use TrackNote\Core\Response;

class CorsMiddleware
{
    private array $config;

    public function __construct(?array $config = null)
    {
        if ($config === null) {
            $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
            $config = $appConfig['cors'];
        }
        $this->config = $config;
    }

    public function handle(Request $request, callable $next): Response
    {
        $origin = $request->header('origin', '');

        if ($request->method() === 'OPTIONS') {
            $response = new Response(null, 204);
            return $this->addCorsHeaders($response, $origin);
        }

        $response = $next($request);
        return $this->addCorsHeaders($response, $origin);
    }

    private function addCorsHeaders(Response $response, string $origin): Response
    {
        $allowedOrigins = $this->config['allowed_origins'] ?? ['*'];

        if (in_array('*', $allowedOrigins)) {
            $response->header('Access-Control-Allow-Origin', '*');
        } elseif (in_array($origin, $allowedOrigins)) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Vary', 'Origin');
        }

        $response->header(
            'Access-Control-Allow-Methods',
            implode(', ', $this->config['allowed_methods'] ?? ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'])
        );

        $response->header(
            'Access-Control-Allow-Headers',
            implode(', ', $this->config['allowed_headers'] ?? ['Content-Type', 'Authorization'])
        );

        if ($this->config['allow_credentials'] ?? false) {
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        if (isset($this->config['max_age'])) {
            $response->header('Access-Control-Max-Age', (string) $this->config['max_age']);
        }

        return $response;
    }
}
