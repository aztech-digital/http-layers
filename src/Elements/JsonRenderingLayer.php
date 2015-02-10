<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Responses\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Aztech\Layers\Layer;

class JsonRenderingLayer implements Layer
{
    private $callable;

    public function __construct(Layer $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(Request $request = null)
    {
        if (! $request) {
            $request = Request::createFromGlobals();
        }

        $callable = $this->callable;

        try {
            $response = $callable($request);
        } catch (HttpException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ], $exception->getStatusCode());
        }

        if ($response instanceof Response) {
            return new JsonResponse($response->getContent(), $response->getStatusCode());
        }

        if (! $response) {
            $response = [];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
