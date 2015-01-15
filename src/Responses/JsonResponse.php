<?php

namespace Aztech\Layers\Responses;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse
{
    private $response;

    public function __construct(array $content, $code)
    {
        $this->response = new Response(json_encode($content), intval($code), [
            'Content-Type' => 'application/json'
        ]);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
