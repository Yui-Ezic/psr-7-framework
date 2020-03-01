<?php


namespace Framework\Http\Pipeline\Exception;

use LogicException;

class MissingResponseException extends LogicException
{
    /**
     * @var callable
     */
    private $middleware;

    public function __construct(callable $middleware)
    {
        parent::__construct('Wrong response type.');
        $this->middleware = $middleware;
    }

    public function getMiddleware() : callable {
        return $this->middleware;
    }
}