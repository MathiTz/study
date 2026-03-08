<?php

declare(strict_types=1);

namespace TrackNote\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private array $groupStack = [];
    /**
     * @param callable(): mixed|mixed[] $handler
     */
    public function get(string $path, callable|array $handler): self
    {
        return $this->addRoute("GET", $path, $handler);
    }
    /**
     * @param callable(): mixed|mixed[] $handler
     */
    public function post(string $path, callable|array $handler): self
    {
        return $this->addRoute("POST", $path, $handler);
    }
    /**
     * @param callable(): mixed|mixed[] $handler
     */
    public function put(string $path, callable|array $handler): self
    {
        return $this->addRoute("PUT", $path, $handler);
    }
    /**
     * @param callable(): mixed|mixed[] $handler
     */
    public function delete(string $path, callable|array $handler): self
    {
        return $this->addRoute("DELETE", $path, $handler);
    }
    /**
     * @param callable(): mixed|mixed[] $handler
     */
    public function options(string $path, callable|array $handler): self
    {
        return $this->addRoute("OPTIONS", $path, $handler);
    }
    /**
     * @param array<int,mixed> $options
     * @param callable(): mixed $callback
     */
    public function group(array $options, callable $callback): self
    {
        $this->groupStack[] = $options;
        $callback($this);
        array_pop($this->groupStack);
        return $this;
    }
    /**
     * @param string|mixed[] $middleware
     */
    public function middleware(string|array $middleware): self
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }
    /**
     * @param callable(): mixed|mixed[] $handler
     */
    private function addRoute(
        string $method,
        string $path,
        callable|array $handler,
    ): self {
        $prefix = "";
        $middlewares = [];

        foreach ($this->groupStack as $group) {
            if (isset($group["prefix"]) && $group["prefix"] !== "") {
                $prefix .= "/" . trim($group["prefix"], "/");
            }
            if (isset($group["middleware"])) {
                $groupMiddleware = is_array($group["middleware"])
                    ? $group["middleware"]
                    : [$group["middleware"]];
                $middlewares = array_merge($middlewares, $groupMiddleware);
            }
        }

        $fullPath = $prefix . "/" . trim($path, "/");
        $fullPath = "/" . trim($fullPath, "/");
        $fullPath = preg_replace("#/+#", "/", $fullPath); // Remove duplicate slashes

        $this->routes[] = [
            "method" => $method,
            "path" => $fullPath,
            "handler" => $handler,
            "middlewares" => $middlewares,
            "pattern" => $this->pathToPattern($fullPath),
        ];

        return $this;
    }

    private function pathToPattern(string $path): string
    {
        $pattern = preg_replace("/\/:([^\/]+)/", '/(?P<$1>[^/]+)', $path);
        return "#^" . $pattern . '$#';
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = $request->path();

        if ($method === "OPTIONS") {
            return new Response(null, 204);
        }

        foreach ($this->routes as $route) {
            if ($route["method"] !== $method) {
                continue;
            }

            if (preg_match($route["pattern"], $path, $matches)) {
                $params = array_filter(
                    $matches,
                    "is_string",
                    ARRAY_FILTER_USE_KEY,
                );
                $request->setParams($params);

                return $this->runMiddlewaresAndHandler(
                    $request,
                    $route["middlewares"],
                    $route["handler"],
                );
            }
        }

        return Response::notFound("Route not found");
    }
    /**
     * @param array<int,mixed> $middlewares
     * @param callable(): mixed|mixed[] $handler
     */
    private function runMiddlewaresAndHandler(
        Request $request,
        array $middlewares,
        callable|array $handler,
    ): Response {
        $middlewareInstances = [];

        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                $middlewareInstances[] = new $middleware();
            } else {
                $middlewareInstances[] = $middleware;
            }
        }

        $next = function (Request $request) use ($handler): Response {
            return $this->callHandler($handler, $request);
        };

        foreach (array_reverse($middlewareInstances) as $middleware) {
            $next = function (Request $request) use (
                $middleware,
                $next,
            ): Response {
                return $middleware->handle($request, $next);
            };
        }

        return $next($request);
    }
    /**
     * @param callable(): mixed|mixed[] $handler
     */
    private function callHandler(
        callable|array $handler,
        Request $request,
    ): Response {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = is_string($class) ? new $class() : $class;
            $result = $controller->$method($request);
        } else {
            $result = $handler($request);
        }

        if ($result instanceof Response) {
            return $result;
        }

        return Response::json($result);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
