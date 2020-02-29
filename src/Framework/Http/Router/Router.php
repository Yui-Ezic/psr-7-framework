<?php


namespace Framework\Http\Router;


use Framework\Http\Router\Exception\RequestNotFoundException;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Psr\Http\Message\ServerRequestInterface;

interface Router
{
    /**
     * @param ServerRequestInterface $request
     * @throws RequestNotMatchedException
     * @return Result
     */
    public function match(ServerRequestInterface $request) : Result;

    /**
     * @param string $name
     * @param array $params
     * @throws RequestNotFoundException
     * @return string
     */
    public function generate(string $name, array $params = []) : string;

    /**
     * @param RouteData $data
     */
    public function addRoute(RouteData $data) : void;
}