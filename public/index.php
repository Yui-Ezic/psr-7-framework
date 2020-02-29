<?php

use Framework\Http\ActionResolver;
use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Framework\Http\Router\Router;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\RequestInterface;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';


### Initialization

$aura = new Aura\Router\RouterContainer();
$routes = $aura->getMap();

$routes->get('home', '/', static function (RequestInterface $request) {
    $name = $request->getQueryParams()['name'] ?: 'Guest';
    return new HtmlResponse('Hello, ' . $name);
});

$routes->get('about', '/about', static function () {
    return new HtmlResponse('This is about page.');
});

$routes->get('blog_show', '/blog/{id}', static function (RequestInterface $request) {
    $id = $request->getAttribute('id');
    if ($id > 2) {
        return new HtmlResponse('Undefined page', 404);
    }
    return new JsonResponse(['id' => $id, 'title' => 'Post #' . $id]);
})->tokens(['id' => '\d+']);

$router = new AuraRouterAdapter($aura);

### Running
$request = ServerRequestFactory::fromGlobals();

try {
    /* @var $router Router */
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    /* @var callable $action */
    $action = (new ActionResolver())->resolve($result->getHandler());
    $response = $action($request);
} catch (RequestNotMatchedException $e) {
    $response = new HtmlResponse('Undefined page', 404);
}


### Postprocessing
$response = $response->withHeader('X-Developed', 'Yui-Ezic');


### Sending
$emitter = new SapiEmitter();
$emitter->emit($response);


