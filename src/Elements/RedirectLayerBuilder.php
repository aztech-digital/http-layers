<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Aztech\Layers\LayerBuilder;
use Aztech\Phinject\Container;

class RedirectLayerBuilder implements LayerBuilder
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildLayer(Layer $nextLayer, array $arguments)
    {
        $execute = null;

        if (isset($arguments['execute'])) {
            $execute = new CallableLayer($this->container->resolve($arguments['execute']));
        }

        return new RedirectLayer($arguments['url'], $execute);
    }
}
