<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\LayerBuilder;
use Aztech\Layers\Layer;

class HttpLayerBuilder implements LayerBuilder
{

    /**
     *
     * (non-PHPdoc) @see \Aztech\LayerBuilder::buildLayer()
     */
    public function buildLayer(Layer $nextLayer, array $arguments)
    {
        $nextLayer = new HandleRedirectResponseLayer($nextLayer);
        $nextLayer = new ErrorHandlingLayer($nextLayer);

        return $nextLayer;
    }
}
