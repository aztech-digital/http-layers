<?php

namespace Aztech\Layers;

interface LayerBuilder
{
    public function buildLayer(Layer $nextLayer, array $arguments);
}
