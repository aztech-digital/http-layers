<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\LayerBuilder;

class JsonRenderingLayerBuilder implements LayerBuilder
{

    /**
     *
     * (non-PHPdoc) @see \Aztech\LayerBuilder::buildLayer()
     */
    public function buildLayer(callable $nextLayer, array $arguments)
    {
        return new JsonRenderingLayer($nextLayer);
    }
}
