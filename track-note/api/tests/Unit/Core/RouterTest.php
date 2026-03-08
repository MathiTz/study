<?php

declare(strict_types=1);

namespace TrackNote\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use TrackNote\Core\Router;
use TrackNote\Core\Request;
use TrackNote\Core\Response;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = new Router();
    }

    public function testCanAddGetRoute(): void
    {
        $this->router->get(
            "/test",
            fn() => Response::success(["message" => "ok"]),
        );

        $routes = $this->router->getRoutes();

        $this->assertCount(1, $routes);
        $this->assertEquals("GET", $routes[0]["method"]);
        $this->assertEquals("/test", $routes[0]["path"]);
    }

    public function testCanAddPostRoute(): void
    {
        $this->router->post("/test", fn() => Response::success());

        $routes = $this->router->getRoutes();

        $this->assertCount(1, $routes);
        $this->assertEquals("POST", $routes[0]["method"]);
    }

    public function testCanAddPutRoute(): void
    {
        $this->router->put("/test", fn() => Response::success());

        $routes = $this->router->getRoutes();

        $this->assertCount(1, $routes);
        $this->assertEquals("PUT", $routes[0]["method"]);
    }

    public function testCanAddDeleteRoute(): void
    {
        $this->router->delete("/test", fn() => Response::success());

        $routes = $this->router->getRoutes();

        $this->assertCount(1, $routes);
        $this->assertEquals("DELETE", $routes[0]["method"]);
    }

    public function testCanGroupRoutes(): void
    {
        $this->router->group(["prefix" => "/api"], function (Router $router) {
            $router->get("/users", fn() => Response::success());
            $router->post("/users", fn() => Response::success());
        });

        $routes = $this->router->getRoutes();

        $this->assertCount(2, $routes);
        $this->assertEquals("/api/users", $routes[0]["path"]);
        $this->assertEquals("/api/users", $routes[1]["path"]);
    }

    public function testCanNestGroups(): void
    {
        $this->router->group(["prefix" => "/api"], function (Router $router) {
            $router->group(["prefix" => "/v1"], function (Router $router) {
                $router->get("/users", fn() => Response::success());
            });
        });

        $routes = $this->router->getRoutes();

        $this->assertCount(1, $routes);
        $this->assertEquals("/api/v1/users", $routes[0]["path"]);
    }

    public function testRoutePatternMatchesParameters(): void
    {
        $this->router->get("/users/:id", fn() => Response::success());

        $routes = $this->router->getRoutes();

        $this->assertMatchesRegularExpression(
            $routes[0]["pattern"],
            "/users/123",
        );
        $this->assertMatchesRegularExpression(
            $routes[0]["pattern"],
            "/users/abc-def-ghi",
        );
        $this->assertDoesNotMatchRegularExpression(
            $routes[0]["pattern"],
            "/users",
        );
        $this->assertDoesNotMatchRegularExpression(
            $routes[0]["pattern"],
            "/users/123/extra",
        );
    }

    public function testRoutePatternMatchesMultipleParameters(): void
    {
        $this->router->get(
            "/users/:userId/posts/:postId",
            fn() => Response::success(),
        );

        $routes = $this->router->getRoutes();

        $this->assertMatchesRegularExpression(
            $routes[0]["pattern"],
            "/users/1/posts/2",
        );
        $this->assertDoesNotMatchRegularExpression(
            $routes[0]["pattern"],
            "/users/1/posts",
        );
    }
}
