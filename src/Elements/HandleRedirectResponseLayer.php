<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirectResponseLayer implements Layer
{

    private $callable;

    public function __construct(Layer $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(Request $request)
    {
        $callable = $this->callable;
        $response = $callable($request);

        if ($response instanceof Response && $response->getStatusCode() == 302) {
            header('Location: ' . $response->getContent());

            return $response;
        }

        return $response;
    }
}
