<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlingLayer implements Layer, LoggerAwareInterface
{

    use LoggerAwareTrait;

    private $controller;

    public function __construct(Layer $controller)
    {
        $this->controller = $controller;
        $this->logger = new NullLogger();
    }

    public function __invoke(Request $request)
    {
        try {
            $controller = $this->controller;

            return $controller($request);
        } catch (\Exception $ex) {
            $this->logger->error($ex);

            return new Response($ex->getMessage(), 500);
        }
    }
}
