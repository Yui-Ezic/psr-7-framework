<?php


namespace Framework\Http\Router\Exception;


use LogicException;
use Throwable;

class RouteNotFoundException extends LogicException
{
    private $name;
    private $params;

    public function __construct(string $name, array $params, Throwable $previous = null)
    {
        parent::__construct('RegexpRoute "' . $name . '" not found.', 0, $previous);
        $this->name = $name;
        $this->params = $params;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getParams() : array
    {
        return $this->params;
    }
}