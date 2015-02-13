<?php

namespace Aztech\Layers;

interface LayerDataInflector
{
    /**
     * Implementations can modify the received data array and must return the inflected array.
     *
     * @param array $data
     * @return array
     */
    public function inflect(array $data);
}