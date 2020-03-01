<?php


namespace Framework\Http\Pipeline;


use Framework\Http\Pipeline\Exception\UnknownMiddlewareTypeException;
use Psr\Http\Server\MiddlewareInterface;
use ReflectionObject;
use function is_array;
use function is_object;
use function is_string;

class MiddlewareResolver
{
    /**
     * @param $handler
     * @return MiddlewareInterface
     */
    public function resolve($handler) : MiddlewareInterface {
        if ($handler instanceof MiddlewareInterface) {
            return $handler;
        }

        if (is_array($handler)) {
            return $this->createPipe($handler);
        }

        if (is_string($handler)) {
            return $this->resolve(new $handler);
        }

        if (is_object($handler)) {
            $reflection = new ReflectionObject($handler);
            if ($reflection->hasMethod('__invoke')) {
                return new SinglePassMiddlewareDecorator($handler);
            }
        }

        throw new UnknownMiddlewareTypeException($handler);
    }

    /**
     * @param array $handlers
     * @return Pipeline
     */
    private function createPipe(array $handlers): Pipeline
    {
        $pipeline = new Pipeline();
        foreach ($handlers as $handler) {
            $pipeline->pipe($this->resolve($handler));
        }
        return $pipeline;
    }
}