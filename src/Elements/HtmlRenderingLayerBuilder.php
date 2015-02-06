<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\LayerBuilder;
use Aztech\Layers\Layer;

class HtmlRenderingLayerBuilder implements LayerBuilder
{

    private $twig;

    private $baseUrl;

    public function __construct(\Twig_Environment $twig, $baseUrl)
    {
        $this->twig = $twig;
        $this->baseUrl = $baseUrl;
    }

    /**
     *
     * (non-PHPdoc) @see \Aztech\LayerBuilder::buildLayer()
     */
    public function buildLayer(Layer $nextLayer, array $arguments)
    {
        $layer = new HtmlRenderingLayer($nextLayer, $this->twig, $arguments[0]);
        $layer->setBaseUrl($this->baseUrl);

        return $layer;
    }
}
