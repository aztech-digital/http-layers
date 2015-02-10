<?php

namespace Aztech\Layers\Tests;

use Aztech\Phinject\Config\ConfigFactory;
use Aztech\Phinject\ContainerFactory;
use Aztech\Layers\Layer;
use Symfony\Component\HttpFoundation\Request;

class LayerActivatorTest extends \PHPUnit_Framework_TestCase
{
    public function testActivatorReturnsRequestedLayers()
    {
        $yaml = <<<YML
config:
    activators:
        layers:
            class: \Aztech\Layers\Phinject\LayerActivator
            defaults:
                http:
                    catch:
                        enabled: true
                        code: 500
                        message: Sorry, an unexpected error has occurred
                html:
                    engine: twig
                    templates: ./
                    baseUrl: "http://localhost/"
                json:
                    engine: fractal

classes:
    controller:
        layers:
            sequence: [ 'http', 'json' ]
            handler:
                isClass: true
                class: \Aztech\Layers\Tests\DummyController
            http:
                catch: true
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);
        $controller = $container->get('controller');

        $this->assertInstanceOf('Aztech\Layers\Layer', $controller);
    }
}

class DummyController implements Layer
{
    public function __invoke(Request $request)
    {
        return [ 'test' => 'success' ];
    }
}