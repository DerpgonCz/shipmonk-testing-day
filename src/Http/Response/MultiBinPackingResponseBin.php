<?php

declare(strict_types=1);

namespace App\Http\Response;

class MultiBinPackingResponseBin
{
    private float $width;
    private float $height;
    private float $length;
    private float $weight;

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getVolume(): float
    {
        return $this->getWidth() * $this->getHeight() * $this->getLength();
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function setLength(float $length): self
    {
        $this->length = $length;

        return $this;
    }
}
