<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\LayerBuilder;
use Aztech\Layers\Layer;

class JsonRenderingLayerBuilder implements LayerBuilder
{

    /**
     *
     * (non-PHPdoc) @see \Aztech\LayerBuilder::buildLayer()
     */
    public function buildLayer(Layer $nextLayer, array $arguments)
    {
        return new JsonRenderingLayer($nextLayer);
    }
}
