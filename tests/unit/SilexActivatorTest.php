<?php

namespace Aztech\Layers\Tests;

use Aztech\Phinject\Config\ConfigFactory;
use Aztech\Phinject\ContainerFactory;
use Aztech\Layers\Layer;
use Symfony\Component\HttpFoundation\Request;

class SilexActivatorTest extends \PHPUnit_Framework_TestCase
{
    public function testActivatorReturnsSilexApplicationWithCorrectDefaults()
    {
        $yaml = <<<YML
config:
    activators:
        silex:
            class: \Aztech\Layers\Phinject\SilexActivator

classes:
    application:
        silex: []
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);
        $application = $container->get('application');

        $this->assertInstanceOf('Silex\Application', $application);
        $this->assertFalse($application['debug']);
    }

    public function testDebugParamIsCorrectlySetToTrue()
    {
        $yaml = <<<YML
config:
    activators:
        silex:
            class: \Aztech\Layers\Phinject\SilexActivator

classes:
    application:
        silex:
            debug: true
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);
        $application = $container->get('application');

        $this->assertTrue($application['debug']);
    }

    public function testDebugParamIsCorrectlySetToFalse()
    {
        $yaml = <<<YML
config:
    activators:
        silex:
            class: \Aztech\Layers\Phinject\SilexActivator

classes:
    application:
        silex:
            debug: false
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);
        $application = $container->get('application');

        $this->assertFalse($application['debug']);
    }

    public function testProvidersAreRegistered()
    {
        $yaml = <<<YML
config:
    activators:
        silex:
            class: \Aztech\Layers\Phinject\SilexActivator

classes:
    application:
        silex: []
        providers:
            Silex\Provider\UrlGeneratorServiceProvider: []
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);
        $application = $container->get('application');

        $this->assertInstanceOf('Symfony\Component\Routing\Generator\UrlGenerator', $application['url_generator']);
    }

    public function testRoutesAreBound()
    {
        $yaml = <<<YML
config:
    activators:
        silex:
            class: \Aztech\Layers\Phinject\SilexActivator

classes:
    controllers:test:
        class: Aztech\Layers\Tests\DummySilexController
        route:
            path: /
    application:
        silex: { debug: true }
        routes:
            root: controllers
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);

        /* @var $application \Silex\Application */
        $application = $container->get('application');
        $application->run();
        /* @var $routes \Silex\ControllerCollection */
        $matcher = $application['url_matcher'];

        $matcher->match('/');
    }

    public function testRoutesOutsideOfRootNSAreNotBound()
    {
        $yaml = <<<YML
config:
    activators:
        silex:
            class: \Aztech\Layers\Phinject\SilexActivator

classes:
    controllers:test:
        class: Aztech\Layers\Tests\DummySilexController
        route:
            path: /
    application:
        silex: { debug: true }
        routes:
            default: controllers:test
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);

        /* @var $application \Silex\Application */
        $application = $container->get('application');
        $application->run();
        /* @var $routes \Silex\ControllerCollection */
        $matcher = $application['url_matcher'];

        $matcher->match('/hello');
    }
}

class DummySilexController
{
    public function __invoke()
    {
        return '';
    }
}
