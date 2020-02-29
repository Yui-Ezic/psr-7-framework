<?php


namespace App\Http\Action;


use Laminas\Diactoros\Response\HtmlResponse;

class AboutAction
{
    public function __invoke()
    {
        return new HtmlResponse('This is about page.');
    }
}