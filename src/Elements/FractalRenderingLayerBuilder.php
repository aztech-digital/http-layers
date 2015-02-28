<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Aztech\Layers\LayerBuilder;
use Aztech\Phinject\Container;
use League\Fractal\Manager;

class FractalRenderingLayerBuilder implements LayerBuilder
{

    private $container;

    private $manager;

    public function __construct(Container $container, Manager $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
    }

    /*
     * (non-PHPdoc) @see \Aztech\LayerBuilder::buildLayer()
     */
    public function buildLayer(Layer $nextLayer, array $arguments)
    {
        $transformerClass = isset($arguments['class']) ? $arguments['class'] : '';
        $isList = isset($arguments['list']) && $arguments['list'] == true;

        if (trim($transformerClass) == '') {
            return $nextLayer;
        }

        return new FractalRenderingLayer($this->container, $this->manager, $nextLayer, $transformerClass, $isList);
    }
}
