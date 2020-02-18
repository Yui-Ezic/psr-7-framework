<?php


namespace Framework\Http\Router\Exception;


use LogicException;

class RequestNotFoundException extends LogicException
{
    private $name;
    private $params;

    public function __construct(string $name, array $params)
    {
        parent::__construct('RegexpRoute "' . $name . '" not found.');
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