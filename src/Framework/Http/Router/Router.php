<?php

namespace Framework\Http\Router;

use Framework\Http\Router\Exception\RequestNotFoundException;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Psr\Http\Message\RequestInterface;

class Router
{
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function match(RequestInterface $request) : Result
    {
        foreach ($this->routes->getRoutes() as $route) {
            if ($result = $route->match($request)) {
                return $result;
            }
        }

        throw new RequestNotMatchedException($request);
    }

    public function generate(string $name, array $params = []) : string
    {
        foreach ($this->routes->getRoutes() as $route) {
            if (null !== $url = $route->generate($name, $params)) {
                return $url;
            }
        }

        throw new RequestNotFoundException($name, $params);
    }
}