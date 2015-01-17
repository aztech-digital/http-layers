<?php

namespace Aztech\Layers\Elements;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErrorHandlingLayer
{
    private $controller;

    public function __construct(callable $controller)
    {
        $this->controller = $controller;
    }

    public function __invoke(Request $request)
    {
        try {
            $controller = $this->controller;

            return $controller($request);
        }
        catch (\Exception $ex) {
            error_log($ex);

            throw new HttpException(500, $ex->getMessage());
        }
    }
}
