<?php


namespace Framework\Http;


class ActionResolver
{
    /**
     * Возвращает callable объект основываясь на $handler который передали.
     *
     * Если handler название класса, то возвращает объект этого класса. Иначе возвращает сам $handler.
     * @param $handler
     * @return callable
     */
    public function resolve($handler) : callable {
        return is_string($handler) ? new $handler : $handler;
    }
}