<?php

namespace Jdw5\SurgeVanguard\Table\Columns\Concerns;

/** @mixin Component */
trait HasTransform
{
    protected null|\Closure $getValueUsing = null;

    /**
     * Transforms the value of the column using the given callback.
     */
    public function transform(\Closure $callback): static
    {
        $this->getValueUsing = $callback;

        return $this;
    }

    public function canTransform(): bool
    {
        return !\is_null($this->getValueUsing);
    }

    public function transformUsing(mixed $value): mixed
    {
        if (! $this->canTransform()) {
            return $value;
        }

        return $this->getTransformed($value);
    }

    public function getTransformed(mixed $value): mixed
    {
        return ($this->getValueUsing)($value);
    }
}