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
use Aztech\Layers\Elements\RedirectLayerBuilder;

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
                $serviceConfig->resolve($this->activatorKey . '.' . $sequence[$i], [], true)->extract()
            );
        }

        $nextLayerConfig = $serviceConfig->resolve($this->activatorKey . '.handler', null);
        $nextLayer = null;

        if ($nextLayerConfig !== null) {
            $nextLayer = $container->resolve($nextLayerConfig);
        }

        return $this->layerBuilder->build($nextLayer, $sequence);
    }

    private function initialize(Container $container)
    {
        $this->layerBuilder->addBuilder('http', new HttpLayerBuilder());

        $this->initializeHtml($container);
        $this->initializeJson($container);
        $this->initializeRedirect($container);
        $this->initializeCustomLayers($container);
    }

    private function initializeHtml(Container $container)
    {
        $baseUrl = $container->resolve($this->config->resolveStrict('defaults.html.baseUrl'));
        $templatePath = $container->resolve($this->config->resolveStrict('defaults.html.templates'));
        $inflectors = $container->resolveMany($this->config->resolveArray('defaults.html.inflectors')->extract());

        $twigLoader = new \Twig_Loader_Filesystem($templatePath);
        $twig = new \Twig_Environment($twigLoader);

        $this->layerBuilder->addBuilder('html', new HtmlRenderingLayerBuilder($twig, $baseUrl, $inflectors));
    }

    private function initializeRedirect(Container $container)
    {
        $this->layerBuilder->addBuilder('redirect', new RedirectLayerBuilder($container));
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

    private function initializeCustomLayers(Container $container)
    {
        $customBuilders = $this->config->resolveArray('custom', []);

        foreach ($customBuilders as $name => $builderConfig) {
            if ($builderConfig instanceof ArrayResolver) {
                $builderConfig = $builderConfig->extract();
            }

            $this->layerBuilder->addBuilder($name, $container->resolve($builderConfig));
        }
    }
}