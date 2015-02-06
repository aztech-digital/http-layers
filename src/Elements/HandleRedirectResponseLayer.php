<?php

namespace Aztech\Layers\Elements;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Aztech\Layers\Layer;

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
