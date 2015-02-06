<?php

namespace Aztech\Layers;

use Symfony\Component\HttpFoundation\Request;

class NullController implements Layer
{
    public function __invoke(Request $request)
    {
        return [];
    }
}
