<?php

namespace Aztech\Layers\Elements;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Aztech\Layers\Layer;
use Aztech\Layers\LayerDataInflector;

class HtmlRenderingLayer implements Layer
{
    private $callable;

    private $sessionManager;

    private $template;

    private $twig;

    private $baseUrl;

    private $inflectors = [];

    public function __construct(Layer $callable, \Twig_Environment $twig, $template)
    {
        $this->callable = $callable;
        $this->template = $template;
        $this->twig = $twig;
        $this->baseUrl = $_SERVER['HTTP_HOST'];
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function addInflectors(array $inflectors)
    {
        foreach ($inflectors as $inflector) {
            $this->addInflector($inflector);
        }
    }

    public function addInflector(LayerDataInflector $inflector)
    {
        $this->inflectors[] = $inflector;
    }

    public function __invoke(Request $request = null)
    {
        if (! $request) {
            $request = Request::createFromGlobals();
        }

        $callable = $this->callable;
        $response = $callable($request ?: Request::createFromGlobals());

        if ($response instanceof Response) {
            return $response;
        }

        if (! $response) {
            $response = [];
        }

        $response['baseUrl'] = $this->baseUrl;

        foreach ($this->inflectors as $inflector) {
            $response = $inflector->inflect($response);
        }

        return $this->twig->render($this->template, $response);
    }
}
