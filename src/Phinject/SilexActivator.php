<?php

namespace Aztech\Layers\Phinject;

use Aztech\Phinject\Activator;
use Aztech\Phinject\ConfigurationAware;
use Aztech\Phinject\Container;
use Aztech\Phinject\Util\ArrayResolver;
use Silex\Application;

class SilexActivator implements Activator, ConfigurationAware
{

    private $activatorKey;

    /**
     * @see \Aztech\Phinject\ConfigurationAware::setConfiguration()
     */
    public function setConfiguration(ArrayResolver $configurationNode)
    {
        $this->activatorKey = $configurationNode->resolve('key');
    }

    /**
     * @see \Aztech\Phinject\Activator::createInstance()
     */
    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        $nodeConfig = $serviceConfig
            ->resolve($this->activatorKey)
            ->extract();

        foreach ($nodeConfig as & $value) {
            $value = $container->resolve($value);
        }

        $silex = new Application($nodeConfig);

        foreach ($serviceConfig->resolveArray('providers', []) as $provider => $values) {
            if (! class_exists($provider, true)) {
                throw new \RuntimeException('Invalid provider: ' . $provider);
            }

            $silex->register(new $provider(), $values->extract());
        }

        $this->bindRoutes($container, $silex, $serviceConfig);

        return $silex;
    }

    private function bindRoutes(Container $container, Application $application, ArrayResolver $serviceConfig)
    {
        $routeBuilder = new SilexRouteBuilder($application, $container);

        $routeBuilder->setDefaultRoute($serviceConfig->resolve('routes.default', ''));
        $routeBuilder->setErrorRoute($serviceConfig->resolve('routes.error', ''));

        $routeBuilder->bindRoutes();
    }
}
