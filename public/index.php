<?php

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

### Initialization
$request = ServerRequestFactory::fromGlobals();

### Action
$name = $request->getQueryParams()['name'] ?: 'Guest';
$response = (new HtmlResponse('Hello, ' . $name))->withHeader("X-Developer", "Yui-Ezic");

### Sending
$emitter = new SapiEmitter();
$emitter->emit($response);

