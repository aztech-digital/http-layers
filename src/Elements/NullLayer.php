<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Symfony\Component\HttpFoundation\Request;

class NullLayer implements Layer
{
    public function __invoke(Request $request)
    {
        return [];
    }
}
