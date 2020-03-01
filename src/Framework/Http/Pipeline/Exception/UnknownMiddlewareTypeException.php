<?php


namespace Framework\Http\Pipeline\Exception;


use LogicException;

class UnknownMiddlewareTypeException extends LogicException
{
    private $handler;

    public function __construct($handler)
    {
        parent::__construct('Unknown middleware type');
        $this->handler = $handler;
    }

    /**
     * @return mixed
     */
    public function getHandler() {
        return $this->handler;
    }
}