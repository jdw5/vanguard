<?php

namespace Jdw5\Vanguard\Table\Columns\Concerns;

use Jdw5\Vanguard\Refining\Sorts\ToggleSort;

/**
 * Trait HasSort
 * 
 * Define whether a column should have toggle sorting enabled.
 * 
 * @property \Jdw5\Vanguard\Refining\Sorts\ToggleSort|null $sort
 */
trait HasSort
{
    protected ?ToggleSort $sort = null;

    /**
     * Define the sorting name, and property to sort by in SQL
     * 
     * @param string|null $name
     * @param string|null $property
     * @return static
     */
    public function sort(?string $name = null, ?string $property = null): static
    {
        $this->sort = ToggleSort::make($property ?? $this->getName(), $name ?? $this->getName());
        return $this;
    }

    /**
     * Check if the column has sorting enabled
     * 
     * @return bool
     */
    public function hasSort(): bool
    {
        return !\is_null($this->getSort());
    }

    /**
     * Get the sort name
     * 
     * @return string|null
     */
    public function getSortName(): ?string
    {
        return $this->hasSort() ? $this->sort->getName() : null;
    }
    
    /**
     * Get the sorting class
     * 
     * @return \Jdw5\Vanguard\Refining\Sorts\ToggleSort|null
     */
    public function getSort(): ?ToggleSort
    {
        return $this->sort;
    }

    /**
     * Check if the column is applied
     * 
     * @return bool
     */
    public function isSorting(): bool
    {
        return $this->hasSort() && $this->sort->sortIsActive();
    }

    /**
     * Get the sorting direction
     * 
     * @return string|null
     */
    public function getDirection(): ?string
    {
        return $this->hasSort() ? $this->sort->getDirection() : null;
    }

    /**
     * Get the next sorting direction
     * 
     * @return string|null
     */
    public function getNextDirection(): ?string
    {
        return $this->hasSort() ? $this->sort->getNextDirection() : null;
    }
}