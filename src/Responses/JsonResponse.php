<?php

namespace Aztech\Layers\Responses;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse extends Response
{
    private $response;

    public function __construct($content, $code)
    {
        if (! is_string($content)) {
            $content = json_encode($content);
        }

        parent::__construct($content, intval($code), [
            'Content-Type' => 'application/json'
        ]);
    }
}
