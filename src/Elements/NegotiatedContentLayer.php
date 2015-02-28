<?php

namespace Aztech\Layers\Elements;

use Aztech\Layers\Layer;
use Negotiation\FormatNegotiator;
use Symfony\Component\HttpFoundation\Request;

class NegotiatedContentLayer implements Layer
{
    private $negotiator;

    private $preferred;

    private $nextLayer;

    private $jsonRenderer;

    private $htmlRenderer;

    public function __construct(Layer $next, Layer $htmlLayer = null, Layer $jsonLayer = null)
    {
        $this->negotiator = new FormatNegotiator();
        $this->nextLayer = $next;

        $this->jsonRenderer = $jsonLayer;
        $this->htmlRenderer = $htmlLayer;

        $this->preferred = [ '*/*' ];

        if ($jsonLayer) {
            array_unshift($this->preferred, 'application/json');
        }

        if ($htmlLayer) {
            array_unshit($this->preferred, 'html');
        }
    }

    public function __invoke(Request $request)
    {
        $layer = $this->nextLayer;
        $response = $layer($request);

        $format = $this->negotiator->getBest($request->headers->get('Accept'), $this->preferred);
        $renderer = null;

        if ($format == 'html' && $this->htmlRenderer) {
            $renderer = $this->htmlRenderer;
        } elseif ($format == 'application/json' && $this->jsonRenderer) {
            $renderer = $this->jsonRenderer;
        }

        return $renderer ? $renderer($response) : $response;
    }
}
