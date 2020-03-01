<?php

use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Pipeline\Pipeline;
use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Framework\Http\Router\Router;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';


### Initialization

$params = [
    'users' => ['admin' => 'password'],
];

$aura = new Aura\Router\RouterContainer();
$routes = $aura->getMap();

$routes->get('home', '/', \App\Http\Action\HelloAction::class);
$routes->get('about', '/about', \App\Http\Action\AboutAction::class);
$routes->get('blog', '/blog', \App\Http\Action\Blog\IndexAction::class);
$routes->get('blog_show', '/blog/{id}', \App\Http\Action\Blog\ShowAction::class)->tokens(['id' => '\d+']);
$routes->get('cabinet', '/cabinet', [new App\Http\Middleware\BasicAuthMiddleware($params['users']), \App\Http\Action\CabinetAction::class]);

$router = new AuraRouterAdapter($aura);
$resolver = new MiddlewareResolver();
$pipeline = new Pipeline();

$pipeline->pipe($resolver->resolve(\App\Http\Middleware\ProfilerMiddleware::class));
$pipeline->pipe($resolver->resolve(\App\Http\Middleware\CredentialsMiddleware::class));

### Running
$request = ServerRequestFactory::fromGlobals();

try {
    /* @var $router Router */
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $handler = $resolver->resolve($result->getHandler());
    $pipeline->pipe($handler);
} catch (RequestNotMatchedException $e) {
}

$response = $pipeline->process($request, new \App\Http\Middleware\NotFoundHandler());


### Sending
$emitter = new SapiEmitter();
$emitter->emit($response);


