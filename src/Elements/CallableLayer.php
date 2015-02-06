<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Symfony\Component\HttpFoundation\Request;

class CallableLayer implements Layer
{

    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Aztech\Layers\Layer::__invoke()
     */
    public function __invoke(Request $request)
    {
        $callable = $this->callable;

        return $callable($request);
    }
}