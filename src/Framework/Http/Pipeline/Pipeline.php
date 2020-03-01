<?php


namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

class Pipeline implements MiddlewareInterface
{
    private $queue;

    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    public function pipe(MiddlewareInterface $middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $default): ResponseInterface
    {
        return (new Next($this->queue, $default))->handle($request);
    }
}