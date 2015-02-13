<?php

namespace Aztech\Layers;

use Aztech\Layers\Elements\CallableLayer;
use Aztech\Layers\Elements\NullLayer;

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
        if (! $this->isValidLayer($nextLayer)) {
            throw new \InvalidArgumentException('Controller must be a callable.');
        }

        $nextLayer = $this->wrapLayerIfNecessary($nextLayer);

        foreach ($keys as $keyValue) {
            $key = is_array($keyValue) ? reset($keyValue) : $keyValue;
            $arguments = is_array($keyValue) ? array_slice($keyValue, 1, count($keyValue) - 1, true) : [];

            $controller = $this->builders[$key]->buildLayer($nextLayer, $arguments);
        }

        return $controller;
    }

    private function isValidLayer($layer)
    {
        return ! ($layer != null && ! is_callable($layer) && ! ($layer instanceof Layer));
    }

    private function wrapLayerIfNecessary($layer)
    {
        if ($layer == null) {
            $layer = new NullLayer();
        }

        if (is_callable($layer) && ! ($layer instanceof Layer)) {
            $layer = new CallableLayer($layer);
        }

        return $layer;
    }
}
