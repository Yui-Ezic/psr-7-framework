<?php


namespace Framework\Http\Router\SimpleRouter;


use Framework\Http\Router\SimpleRouter\Route\RegexpRoute;
use Framework\Http\Router\SimpleRouter\Route\Route;

/**
 * Class RouteCollection
 *
 * Содержит коллекцию маршрутов для сайта.
 *
 * @package Framework\Http\SimpleRouter
 */
class RouteCollection
{
    private $routes = [];

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function add(string $name, string $pattern, $handler, array $methods, array $tokens = []): void
    {
        $this->addRoute(new RegexpRoute($name, $pattern, $handler, $methods, $tokens));
    }

    public function any(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->addRoute(new RegexpRoute($name, $pattern, $handler, [], $tokens));
    }

    public function get(string $name, string $pattern, $handler, array $tokens = []) : void
    {
        $this->addRoute(new RegexpRoute($name, $pattern, $handler, ['GET'], $tokens));
    }

    public function post(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->addRoute(new RegexpRoute($name, $pattern, $handler, ['POST'], $tokens));
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}