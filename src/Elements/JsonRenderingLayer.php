<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Aztech\Layers\Responses\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonRenderingLayer implements Layer
{
    private $callable;

    private $mergeBody;

    public function __construct(Layer $callable, $mergeBody)
    {
        $this->callable = $callable;
        $this->mergeBody = (bool) $mergeBody;
    }

    public function __invoke(Request $request = null)
    {
        if (! $request) {
            $request = Request::createFromGlobals();
        }

        if ($this->mergeBody) {
            $request = $this->mergeRequestBody($request);
        }

        return $this->runController($request);
    }

    private function mergeRequestBody(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        if (is_array($body)) {
            $request->request->add($body);
        }

        return $request;
    }

    private function runController(Request $request)
    {
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
