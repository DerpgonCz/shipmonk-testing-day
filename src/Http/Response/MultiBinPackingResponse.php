<?php

declare(strict_types=1);

namespace App\Http\Response;

class MultiBinPackingResponse
{
    /** @var \App\Http\Response\MultiBinPackingResponseBin[] */
    private array $binsPacked = [];

    /** @var \App\Http\Response\MultiBinPackingResponseError[] */
    private array $errors = [];

    /** @var \App\Http\Response\MultiBinPackingResponseItem[] */
    private array $notPackedItems = [];

    public function hasBinsPacked(): bool
    {
        return count($this->binsPacked) !== 0;
    }

    public function getBinsPacked(): array
    {
        return $this->binsPacked;
    }

    /**
     * @param \App\Http\Response\MultiBinPackingResponseBin[] $binsPacked
     */
    public function setBinsPacked(array $binsPacked): self
    {
        $this->binsPacked = $binsPacked;

        return $this;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) !== 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function hasNotPackedItems(): bool
    {
        return count($this->notPackedItems) !== 0;
    }

    public function getNotPackedItems(): array
    {
        return $this->notPackedItems;
    }

    public function setNotPackedItems(array $notPackedItems): self
    {
        $this->notPackedItems = $notPackedItems;

        return $this;
    }
}
