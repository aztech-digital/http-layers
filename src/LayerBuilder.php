<?php

namespace Aztech\Layers;

interface LayerBuilder
{
    public function buildLayer(callable $nextLayer, array $arguments);
}
