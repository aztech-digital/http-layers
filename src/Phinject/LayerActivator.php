<?php

namespace Aztech\Layers\Phinject;

use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\ConfigurationAware;
use Aztech\Layers\LayeredControllerFactory;
use Aztech\Layers\Elements\HttpLayerBuilder;
use Aztech\Layers\Elements\HtmlRenderingLayer;
use Aztech\Layers\Elements\HtmlRenderingLayerBuilder;
use Aztech\Layers\Elements\FractalRenderingLayerBuilder;
use Aztech\Layers\Elements\JsonRenderingLayerBuilder;

class LayerActivator implements Activator, ConfigurationAware
{
    private $config;

    private $activatorKey;

    private $initialized = false;

    private $layerBuilder;

    public function __construct()
    {
        $this->layerBuilder = new LayeredControllerFactory();
    }

    public function setConfiguration(ArrayResolver $configurationNode)
    {
        $this->config = $configurationNode;
        $this->activatorKey = $this->config->resolve('key');
    }

    /*
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Activator::createInstance()
     */
    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        if (! $this->initialized) {
            $this->initialize($container);
        }

        $sequence = $serviceConfig
            ->resolveStrict($this->activatorKey . '.sequence')
            ->extract();

        for ($i = 0; $i < count($sequence); $i++) {
            $sequence[$i] = array_merge(
                [
                    $sequence[$i]
                ],
                array_values(
                    $serviceConfig->resolve($this->activatorKey . '.' . $sequence[$i], [], true)->extract()
                )
            );
        }

        $nextLayerConfig = $serviceConfig->resolveStrict($this->activatorKey . '.handler');
        $nextLayer = $container->resolve($nextLayerConfig);

        return $this->layerBuilder->build($nextLayer, $sequence);
    }

    private function initialize(Container $container)
    {
        $this->layerBuilder->addBuilder('http', new HttpLayerBuilder());

        $this->initializeHtml($container);
        $this->initializeJson($container);
    }

    private function initializeHtml(Container $container)
    {
        $baseUrl = $container->resolve($this->config->resolveStrict('defaults.html.baseUrl'));
        $templatePath = $container->resolve($this->config->resolveStrict('defaults.html.templates'));
        $inflectors = $container->resolveMany($this->config->resolveArray('inflectors'));

        $twigLoader = new \Twig_Loader_Filesystem($templatePath);
        $twig = new \Twig_Environment($twigLoader);

        $this->layerBuilder->addBuilder('html', new HtmlRenderingLayerBuilder($twig, $baseUrl, $inflectors));
    }

    private function initializeJson(Container $container)
    {
        $manager = new \League\Fractal\Manager();
        $transformationEngine = null;

        if ($this->config->resolve('defaults.json.engine', null) == 'fractal') {
            $transformationEngine = new FractalRenderingLayerBuilder($container, $manager);
        }

        $this->layerBuilder->addBuilder('json', new JsonRenderingLayerBuilder($transformationEngine));
    }
}