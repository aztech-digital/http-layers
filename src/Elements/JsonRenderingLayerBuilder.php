<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\LayerBuilder;
use Aztech\Layers\Layer;

class JsonRenderingLayerBuilder implements LayerBuilder
{

    private $transformationLayerBuilder;

    public function __construct(LayerBuilder $transformationLayerBuilder = null)
    {
        $this->transformationLayerBuilder = $transformationLayerBuilder;
    }

    /**
     *
     * (non-PHPdoc) @see \Aztech\LayerBuilder::buildLayer()
     */
    public function buildLayer(Layer $nextLayer, array $arguments)
    {
        if ($this->transformationLayerBuilder) {
            $nextLayer = $this->transformationLayerBuilder->buildLayer($nextLayer, $arguments);
        }

        return new JsonRenderingLayer($nextLayer);
    }
}
