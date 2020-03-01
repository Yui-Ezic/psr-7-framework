<?php


namespace Framework\Http\Pipeline;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

class Next implements RequestHandlerInterface
{
    private $queue;
    private $next;

    public function __construct(SplQueue $queue, RequestHandlerInterface $next)
    {
        $this->queue = $queue;
        $this->next = $next;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return $this->next->handle($request);
        }

        /* @var $middleware \Psr\Http\Server\MiddlewareInterface */
        $middleware = $this->queue->dequeue();

        return $middleware->process($request, $this);
    }
}