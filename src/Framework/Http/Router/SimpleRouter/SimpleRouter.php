<?php

namespace Framework\Http\Router\SimpleRouter;

use Framework\Http\Router\Result;
use Framework\Http\Router\Router;
use Framework\Http\Router\RouteData;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Http\Router\Exception\RequestNotFoundException;
use Framework\Http\Router\Exception\RequestNotMatchedException;

class SimpleRouter implements Router
{
    /**
     * @var RouteCollection
     */
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @inheritDoc
     */
    public function match(ServerRequestInterface $request) : Result
    {
        foreach ($this->routes->getRoutes() as $route) {
            if ($result = $route->match($request)) {
                return $result;
            }
        }

        throw new RequestNotMatchedException($request);
    }

    /**
     * @inheritDoc
     */
    public function generate(string $name, array $params = []) : string
    {
        foreach ($this->routes->getRoutes() as $route) {
            if (null !== $url = $route->generate($name, $params)) {
                return $url;
            }
        }

        throw new RequestNotFoundException($name, $params);
    }

    /**
     * @inheritDoc
     */
    public function addRoute(RouteData $data): void
    {
        $this->routes->add($data->name, $data->path, $data->handler, $data->methods, $data->options);
    }
}