<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Responses\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonRenderingLayer
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(Request $request = null)
    {
        if (! $request) {
            $request = Request::createFromGlobals();
        }

        $callable = $this->callable;
        $response = $callable($request);

        if ($response instanceof Response) {
            return (new JsonResponse($response->getContent(), $response->getStatusCode()))->getResponse();
        }

        if (! $response) {
            $response = [];
        }

        return (new JsonResponse($response, 200))->getResponse();
    }
}
