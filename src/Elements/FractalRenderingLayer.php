<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Aztech\Layers\Pagination\PaginatedResult;
use Aztech\Layers\Pagination\PaginatedResultAdapter;
use Aztech\Phinject\Container;
use League\Fractal\Manager;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Symfony\Component\HttpFoundation\Request;

class FractalRenderingLayer implements Layer
{

    private $controller;

    private $isList = false;

    private $transformer;

    private $manager;

    public function __construct(Container $container, Manager $manager, Layer $controller, $transformer, $isList)
    {
        $this->controller = $controller;
        $this->isList = $isList;
        $this->manager = $manager;
        $this->transformer = $transformer;

        if (class_exists($this->transformer, true)) {
            $this->transformer = $container->resolve([ 'class' => $this->transformer ]);
        } else {
            $this->transformer = $container->resolve($this->transformer);
        }

        if (! ($this->transformer instanceof TransformerAbstract)) {
            throw new \RuntimeException('Invalid transformer: ' . serialize($this->transformer));
        }
    }

    public function __invoke(Request $request)
    {
        $controller = $this->controller;
        $data = $controller($request);

        if ($data == null) {
            return null;
        }

        if ($this->isList) {
            $resource = new Collection(($data instanceof PaginatedResult) ? $data->getResults() : $data, $this->transformer);

            if ($data instanceof PaginatedResult) {
                $resource->setPaginator(new PaginatedResultAdapter($data));
            }
        } else {
            $resource = new Item($data, $this->transformer);
        }

        return $this->manager->createData($resource)->toArray();
    }
}
