<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Core\Router;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function testAddRouteAndMatchRoute(): void
    {
        $this->router->addRoute('GET', '/custom-test-route', ['TestController', 'testMethod']);

        $reflection = new ReflectionClass($this->router);
        $method = $reflection->getMethod('matchRoute');

        $matched = $method->invoke($this->router, 'GET', '/custom-test-route');
        $this->assertNotNull($matched);
        $this->assertSame(['TestController', 'testMethod'], $matched['handler']);
        $this->assertEmpty($matched['params']);
    }

    public function testRouteWithDynamicParameters(): void
    {
        $this->router->addRoute('GET', '/posts/{slug}/comments/{id}', ['CommentController', 'show']);

        $reflection = new ReflectionClass($this->router);
        $method = $reflection->getMethod('matchRoute');

        $matched = $method->invoke($this->router, 'GET', '/posts/my-first-post/comments/42');
        $this->assertNotNull($matched);
        $this->assertSame('my-first-post', $matched['params']['slug']);
        $this->assertSame('42', $matched['params']['id']);
    }

    public function testRouteMethodMismatchReturnsNull(): void
    {
        $this->router->addRoute('POST', '/api/save', ['ApiController', 'save']);

        $reflection = new ReflectionClass($this->router);
        $method = $reflection->getMethod('matchRoute');

        $matched = $method->invoke($this->router, 'GET', '/api/save');
        $this->assertNull($matched);
    }

    public function testRouteNotFoundReturnsNull(): void
    {
        $reflection = new ReflectionClass($this->router);
        $method = $reflection->getMethod('matchRoute');

        $matched = $method->invoke($this->router, 'GET', '/completely-nonexistent-path-12345');
        $this->assertNull($matched);
    }
}
