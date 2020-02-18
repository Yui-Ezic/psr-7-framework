<?php


namespace Framework\Http\Router\Exception;

use LogicException;
use Psr\Http\Message\RequestInterface;

class RequestNotMatchedException extends LogicException
{
    private $request;

    public function __construct(RequestInterface $request)
    {
        parent::__construct('Matches not found.');
        $this->request = $request;
    }

    public function getRequest() : RequestInterface
    {
        return $this->request;
    }

}