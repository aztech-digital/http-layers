<?php

namespace Aztech\Layers;

class LayeredControllerFactory
{
    /**
     *
     * @var LayerBuilder[]
     */
    private $builders;

    public function addBuilder($key, LayerBuilder $builder)
    {
        $this->builders[$key] = $builder;
    }

    public function build($controller, array $keys)
    {
        if (! is_callable($controller)) {
            throw new \InvalidArgumentException('Controller must be a callable.');
        }

        foreach ($keys as $keyValue) {
            $key = is_array($keyValue) ? $keyValue[0] : $keyValue;
            $arguments = is_array($keyValue) ? array_slice($keyValue, 1) : [];

            $controller = $this->builders[$key]->buildLayer($controller, $arguments);
        }

        return $controller;
    }
}
