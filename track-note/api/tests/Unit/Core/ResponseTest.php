<?php

declare(strict_types=1);

namespace TrackNote\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use TrackNote\Core\Response;

class ResponseTest extends TestCase
{
    public function testJsonCreatesResponseWithData(): void
    {
        $response = Response::json(['key' => 'value'], 200);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['key' => 'value'], $response->getBody());
    }

    public function testSuccessCreatesSuccessResponse(): void
    {
        $response = Response::success(['data' => 'test'], 'Operation successful');

        $this->assertEquals(200, $response->getStatusCode());
        $body = $response->getBody();
        $this->assertTrue($body['success']);
        $this->assertEquals('Operation successful', $body['message']);
        $this->assertEquals(['data' => 'test'], $body['data']);
    }

    public function testCreatedReturns201(): void
    {
        $response = Response::created(['id' => '123']);

        $this->assertEquals(201, $response->getStatusCode());
        $body = $response->getBody();
        $this->assertTrue($body['success']);
    }

    public function testNoContentReturns204(): void
    {
        $response = Response::noContent();

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertNull($response->getBody());
    }

    public function testErrorCreatesErrorResponse(): void
    {
        $response = Response::error('Something went wrong', 400);

        $this->assertEquals(400, $response->getStatusCode());
        $body = $response->getBody();
        $this->assertFalse($body['success']);
        $this->assertEquals('Something went wrong', $body['message']);
    }

    public function testNotFoundReturns404(): void
    {
        $response = Response::notFound('Resource not found');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUnauthorizedReturns401(): void
    {
        $response = Response::unauthorized();

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testForbiddenReturns403(): void
    {
        $response = Response::forbidden();

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testValidationErrorReturns422(): void
    {
        $errors = ['email' => ['Email is required']];
        $response = Response::validationError($errors);

        $this->assertEquals(422, $response->getStatusCode());
        $body = $response->getBody();
        $this->assertEquals($errors, $body['errors']);
    }

    public function testServerErrorReturns500(): void
    {
        $response = Response::serverError();

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testCanSetCustomHeaders(): void
    {
        $response = Response::success();
        $response->header('X-Custom-Header', 'custom-value');

        $headers = $response->getHeaders();
        $this->assertEquals('custom-value', $headers['X-Custom-Header']);
    }

    public function testDefaultContentTypeIsJson(): void
    {
        $response = Response::success();

        $headers = $response->getHeaders();
        $this->assertEquals('application/json', $headers['Content-Type']);
    }
}
