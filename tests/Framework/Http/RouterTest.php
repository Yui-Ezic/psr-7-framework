<?php

namespace Tests\Framework\Http;

use Framework\Http\Router\Exception\RequestNotFoundException;
use InvalidArgumentException;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Framework\Http\Router\RouteCollection;
use Framework\Http\Router\Router;
use Psr\Http\Message\RequestInterface;

class RouterTest extends TestCase
{
    public function testCorrectMethod(): void
    {
        $routes = new RouteCollection();

        $routes->get($nameGet = 'blog', '/blog', $handlerGet = 'handler_get');
        $routes->post($namePost = 'blog_edit', '/blog', $handlerPost = 'handler_post');

        $routes->get("blog_view", '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new Router($routes);

        $result = $router->match($this->buildRequest('GET', '/blog'));
        self::assertEquals($nameGet, $result->getName());
        self::assertEquals($handlerGet, $result->getHandler());

        $result = $router->match($this->buildRequest('POST', '/blog'));
        self::assertEquals($namePost, $result->getName());
        self::assertEquals($handlerPost, $result->getHandler());
    }

    public function testMissingMethod(): void
    {
        $routes = new RouteCollection();

        $routes->get('blog', '/blog', 'handler_get');

        $router = new Router($routes);

        $this->expectException(RequestNotMatchedException::class);
        $router->match($this->buildRequest('POST', '/blog'));
    }

    public function testCorrectAttributes(): void
    {
        $routes = new RouteCollection();

        $routes->get($nameBlog = 'blog', '/blog/{id}', $handlerBlog = 'handler_blog', ['id' => '\d+']);
        $routes->get($namePost = 'post', '/post/{post_id}/{comment_id}', $handlerPost = 'handler_post', [
            'post_id' => '\d+',
        ]);

        $router = new Router($routes);

        $result = $router->match($this->buildRequest("GET", '/blog/2'));
        self::assertEquals($handlerBlog, $result->getHandler());
        self::assertEquals($nameBlog, $result->getName());
        self::assertEquals(['id' => 2], $result->getAttributes());

        $result = $router->match($this->buildRequest("GET", '/post/4/5'));
        self::assertEquals($handlerPost, $result->getHandler());
        self::assertEquals($namePost, $result->getName());
        self::assertEquals(['post_id' => 4, 'comment_id' => 5], $result->getAttributes());
    }

    public function testIncorrectAttributes(): void
    {
        $routes = new RouteCollection();

        $routes->get($name = 'blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new Router($routes);

        $this->expectException(RequestNotMatchedException::class);
        $router->match($this->buildRequest('GET', '/blog/slug'));
    }

    public function testGenerate(): void
    {
        $routes = new RouteCollection();

        $routes->get('blog', '/blog', 'handler');
        $routes->get('blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new Router($routes);

        self::assertEquals('/blog', $router->generate('blog'));
        self::assertEquals('/blog/5', $router->generate('blog_show', ['id' => 5]));
    }

    public function testGenerateMissingAttributes(): void
    {
        $routes = new RouteCollection();

        $routes->get($name = 'blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new Router($routes);

        $this->expectException(InvalidArgumentException::class);
        $router->generate('blog_show', ['slug' => 'post']);
    }

    public function testGenerateNonexistentRoute(): void
    {
        $routes = new RouteCollection();
        $router = new Router($routes);

        $this->expectException(RequestNotFoundException::class);
        $router->generate('post');
    }

    private function buildRequest($method, $uri): RequestInterface
    {
        return (new ServerRequest())
            ->withMethod($method)
            ->withUri(new Uri($uri));
    }
}