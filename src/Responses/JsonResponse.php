<?php

namespace Aztech\Layers\Responses;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse
{
    private $response;

    public function __construct($content, $code)
    {
        if (! is_string($content)) {
            json_encode($content);
        }

        $this->response = new Response($content, intval($code), [
            'Content-Type' => 'application/json'
        ]);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
