<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\LayerBuilder;

class HttpLayerBuilder implements LayerBuilder
{
    /**
     *
     * (non-PHPdoc) @see \Aztech\LayerBuilder::buildLayer()
     */
    public function buildLayer(LayerBuilder $nextLayer, array $arguments)
    {
        $nextLayer = new HandleRedirectResponseLayer($nextLayer);
        $nextLayer = new ErrorHandlingLayer($nextLayer);

        return $nextLayer;
    }
}
