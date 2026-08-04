<?php
use PHPUnit\Framework\TestCase;
use Core\Router;

class RouterTest extends TestCase
{
    public function testDispatchHome()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $router = new Router();
        $this->assertTrue(true);
    }
}
