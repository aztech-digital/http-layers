<?php

namespace Aztech\Layers\Phinject;

use Aztech\Phinject\ObjectContainer;
use Aztech\Phinject\Util\ArrayResolver;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SilexRouteBuilder
{

    private $application;

    private $container;

    private $hasDefaultController = false;

    private $defaultControllerKey = null;

    private $hasErrorController = false;

    private $errorControllerKey = null;

    public function __construct(Application $application, ObjectContainer $container)
    {
        $this->application = $application;
        $this->container = $container;
    }

    public function setErrorRoute($errorControllerKey)
    {
        $this->errorControllerKey = (string) $errorControllerKey;
        $this->hasErrorController = true;
    }

    public function setDefaultRoute($controllerKey, $disable = false)
    {
        $this->defaultControllerKey = (string) $controllerKey;
        $this->hasDefaultController = ! empty($this->defaultControllerKey) &&  ! $disable;
    }

    public function bindRoutes()
    {
        $config = $this->container->getGlobalConfig();

        foreach ($config->resolve('classes') as $serviceName => $serviceConfig) {
            $this->bindRoute($serviceConfig, $serviceName);
        }

        if ($this->hasDefaultController) {
            $this->application->match('{url}', $this->deferExecution($this->defaultControllerKey))
                ->assert('url', '.+');
        }

        if ($this->application['debug'] == false && $this->hasErrorController) {
            $this->application->error($this->getErrorHandler());
        }
    }

    public function bindRoute(ArrayResolver $serviceConfig, $serviceName)
    {
        $routePath = $serviceConfig->resolve('route.path', $serviceConfig->resolve('route'));
        $routeName = $serviceConfig->resolve('route.name', $serviceName);

        $methods = array_values($serviceConfig->resolveArray('route.methods', [ 'get' ])->extract());
        $asserts = $serviceConfig->resolveArray('route.assert', [ ])->extract();

        if (! $routePath || ! is_scalar($routePath)) {
            return;
        }

        foreach ($methods as $index => $method) {
            $route = $this->application->{$method}($routePath, $this->deferExecution($serviceName));

            if ($routeName) {
                $routeName = $index > 0 ? $routeName . ':' . $method : $routeName;
                $route->bind($routeName);
            }

            foreach ($asserts as $param => $assert) {
                $route->assert($param, $assert);
            }
        }
    }

    private function deferExecution($serviceName)
    {
        return function (Request $request) use ($serviceName) {
            $controller = $this->container->get($serviceName);

            return $controller($request);
        };
    }

    private function getErrorHandler()
    {
        $container = $this->container;
        $errorCallback = null;

        if ($this->hasErrorController) {
            $errorCallback = $this->errorControllerKey;
        }

        return function (\Exception $exception, $code) use ($container, $errorCallback) {
            if ($errorCallback != null) {
                $controller = $container->resolve($errorCallback);

                return $controller(Request::createFromGlobals());
            }
        };
    }
}
