<?php


namespace Framework\Http\Router\SimpleRouter\Route;


use Framework\Http\Router\Result;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

/**
 * Реализация интерфейса Route на основе регулярных выражений
 *
 * Class RegexpRoute
 * @package Framework\Http\SimpleRouter\Route
 */
class RegexpRoute implements Route
{
    public $name;
    public $pattern;
    public $handler;
    public $methods;
    public $tokens;

    public function __construct($name, $pattern, $handler, array $methods, array $tokens = [])
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->tokens = $tokens;
        $this->methods = $methods;
    }

    public function match(RequestInterface $request) : ?Result
    {
        // Проверяем метод
        if ($this->methods && !in_array($request->getMethod(), $this->methods, true)) {
            return null;
        }

        // Заменяем по шаблону: ['{var}', ['var' => '\d+']] -> '(?P<var>\d+)'
        $pattern = preg_replace_callback('~{([^}]+)}~', function ($matches) {
            $argument = $matches[1];
            $replace = $this->tokens[$argument] ?? '[^}]+';
            return '(?P<' . $argument . '>' . $replace . ')';
        }, $this->pattern);

        // Проверяем совпадение пути запроса с шаблоном
        if (!preg_match('~^' . $pattern . '$~i', $request->getUri()->getPath(), $matches)) {
            return null;
        }

        return new Result(
            $this->name,
            $this->handler,
            array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY)
        );
    }

    public function generate(string $name, array $params = []) : ?string
    {
        $arguments = array_filter($params);

        if ($name !== $this->name) {
            return null;
        }

        return preg_replace_callback('~{([^}]+)}~', static function ($matches) use (&$arguments) {
            $argument = $matches[1];
            if (!array_key_exists($argument, $arguments)) {
                throw new InvalidArgumentException('Missing parameter "' . $argument . '"');
            }
            return $arguments[$argument];
        }, $this->pattern);
    }
}