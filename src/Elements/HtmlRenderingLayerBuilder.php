<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Aztech\Layers\LayerBuilder;
use Aztech\Layers\LayerDataInflector;

class HtmlRenderingLayerBuilder implements LayerBuilder
{

    private $twig;

    private $baseUrl;

    private $inflectors = [];

    public function __construct(\Twig_Environment $twig, $baseUrl, array $inflectors = [])
    {
        $this->twig = $twig;
        $this->baseUrl = $baseUrl;

        foreach ($inflectors as $inflector) {
            $this->addInflector($inflector);
        }
    }

    public function addInflector(LayerDataInflector $inflector)
    {
        $this->inflectors[] = $inflector;
    }

    /**
     *
     * (non-PHPdoc) @see \Aztech\LayerBuilder::buildLayer()
     */
    public function buildLayer(Layer $nextLayer, array $arguments)
    {
        $layer = new HtmlRenderingLayer($nextLayer, $this->twig, reset($arguments));
        $layer->setBaseUrl($this->baseUrl);
        $layer->addInflectors($this->inflectors);

        return $layer;
    }
}
