<?php


namespace Framework\Http\Router\Route;


use Framework\Http\Router\Result;
use Psr\Http\Message\RequestInterface;

interface Route
{
    /**
     * Проверяет может ли данный путь(Route) обработать запрос.
     * Возвращает класс Result в случаи успеха, и null иначе.
     *
     * @param RequestInterface $request Запрос
     * @return Result|null
     */
    public function match(RequestInterface $request): ?Result;

    /**
     * Формирует url к даному пути(Route)
     * Возращает Null если переданно неправильное имя пути.
     *
     * @param string $name
     * @param array $params
     * @return string|null
     */
    public function generate(string $name, array $params = []): ?string;
}