<?php

namespace Aztech\Layers;

use Symfony\Component\HttpFoundation\Request;

interface Layer
{
    public function __invoke(Request $request);
}