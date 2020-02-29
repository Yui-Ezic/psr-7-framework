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

$routes->get('home', '/', \App\Http\Action\HelloAction::class);
$routes->get('about', '/about', \App\Http\Action\AboutAction::class);
$routes->get('blog', '/blog', \App\Http\Action\Blog\IndexAction::class);
$routes->get('blog_show', '/blog/{id}', \App\Http\Action\Blog\ShowAction::class)->tokens(['id' => '\d+']);

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


