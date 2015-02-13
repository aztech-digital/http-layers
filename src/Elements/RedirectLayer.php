<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Symfony\Component\HttpFoundation\Request;

class RedirectLayer implements Layer
{
    private $executeLayer;

    private $redirectUrl;

    public function __construct($redirectUrl, Layer $executeLayer = null)
    {
        $this->executeLayer = $executeLayer;
        $this->redirectUrl = $redirectUrl;
    }

    public function __invoke(Request $request)
    {
        if ($this->executeLayer) {
            $execute = $this->executeLayer;
            $execute($request);
        }

        header('Location: ' . $this->redirectUrl);
        exit();
    }
}