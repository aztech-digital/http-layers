<?php

namespace Aztech\Layers;

class NullController
{
    public function __invoke()
    {
        return [];
    }
}
