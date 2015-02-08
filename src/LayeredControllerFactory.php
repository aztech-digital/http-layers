<?php

namespace Aztech\Layers;

use Aztech\Layers\Elements\CallableLayer;

class LayeredControllerFactory
{
    /**
     *
     * @var LayerBuilder[]
     */
    private $builders = [];

    public function addBuilder($key, LayerBuilder $builder)
    {
        $this->builders[$key] = $builder;
    }

    public function build($nextLayer, array $keys)
    {
        if (! is_callable($nextLayer) && ! ($nextLayer instanceof Layer)) {
            throw new \InvalidArgumentException('Controller must be a callable.');
        }

        if (is_callable($nextLayer)) {
            $nextLayer = new CallableLayer($nextLayer);
        }

        foreach ($keys as $keyValue) {
            $key = is_array($keyValue) ? $keyValue[0] : $keyValue;
            $arguments = is_array($keyValue) ? array_slice($keyValue, 1) : [];

            $controller = $this->builders[$key]->buildLayer($nextLayer, $arguments);
        }

        return $controller;
    }
}
