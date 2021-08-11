<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *     @ORM\UniqueConstraint(name="key_unique_idx", columns={"cache_key"})
 * })
 * TODO: Add Created At field for lifetime?
 */
class Packing
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $cacheKey;

    /**
     * @ORM\Column(type="float")
     */
    private float $width;

    /**
     * @ORM\Column(type="float")
     */
    private float $height;

    /**
     * @ORM\Column(type="float")
     */
    private float $length;

    /**
     * @ORM\Column(type="float")
     */
    private float $weight;

    public function __construct(
        string $cacheKey,
        float $width,
        float $height,
        float $length,
        float $weight
    ) {
        $this->cacheKey = $cacheKey;
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->weight = $weight;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }
}
