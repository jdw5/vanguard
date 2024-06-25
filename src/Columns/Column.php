<?php

namespace Jdw5\Vanguard\Columns;

use Closure;
use Jdw5\Vanguard\Concerns\HasName;
use Jdw5\Vanguard\Concerns\HasDisplay;
use Jdw5\Vanguard\Concerns\HasMetadata;
use Jdw5\Vanguard\Columns\Concerns\IsKey;
use Jdw5\Vanguard\Columns\Concerns\HasSort;
use Jdw5\Vanguard\Columns\Concerns\HasFallback;
use Jdw5\Vanguard\Columns\Concerns\HasTransform;
use Jdw5\Vanguard\Columns\Concerns\IsSearchable;
use Jdw5\Vanguard\Columns\Concerns\IsSortable;
use Jdw5\Vanguard\Columns\Concerns\IsToggleable;
use Jdw5\Vanguard\Concerns\HasAuthorization;
use Jdw5\Vanguard\Columns\Enums\Breakpoint;

class Column extends BaseColumn
{
    use HasName;
    use HasMetadata;
    use HasFallback;
    use HasTransform;
    use HasSort;
    use HasAuthorization;
    use IsKey;
    use HasDisplay;
    use IsSortable;
    use IsSearchable;
    use IsToggleable;

    public function __construct(
        string|Closure $name, 
        string|Closure $label = null,
        bool $sortable = false,
        bool $searchable = false,
        bool $toggleable = false,
        Breakpoint|string $breakpoint = Breakpoint::NONE,
        Closure|bool $authorize = null,
        mixed $fallback = null,
        bool $asHeading = true,
        bool $srOnly = false,
        Closure $transform = null,
    ) {
        $this->setName($name);
        $this->setLabel($label ?? $this->toLabel($this->getName()));
        $this->setSortability($sortable);
        $this->setSearchability($searchable);
        $this->setToggleability($toggleable);
        $this->setBreakpoint($breakpoint);
        $this->setAuthorize($authorize);
        $this->setFallback($fallback);
        $this->setShow($asHeading);
        $this->setSrOnly($srOnly);
        $this->setTransform($transform);
        $this->setType('col:');
    }

    /**
     * Statically create the column
     */
    public static function make(
        string $name, 
        string $label = null,
        bool $sortable = false,
        bool $searchable = false,
        bool $toggleable = false,
        Breakpoint|string $breakpoint = Breakpoint::NONE,
        Closure|bool $authorize = null,
        mixed $fallback = null,
        bool $asHeading = true,
        bool $srOnly = false,
        Closure $transform = null,
    ): static {
        return new static($name, $label, $sortable, $searchable, $toggleable, $breakpoint, $authorize, $fallback, $asHeading, $srOnly, $transform);
    }

    /**
     * Convert the column to an array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            /** Column information */
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'metadata' => $this->getMetadata(),
            'fallback' => $this->getFallback(),

            /** Display options for frontend */
            'display' => $this->isShown(),
            'breakpoint' => $this->getBreakpoint(),
            'srOnly' => $this->isSrOnly(),

            /** Sorting options */
            'has_sort' => $this->hasSort(),
            'active' => $this->isSorting(),
            'direction' => $this->getDirection(),
            'next_direction' => $this->getNextDirection(),
            'sort_field' => $this->getSortName(),
        ];
    }

    /**
     * Serialize the column for JSON
     * 
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function apply(mixed $value): mixed
    {
        if (is_null($value)) return $this->getFallback();
        if ($this->hasTransform()) return $this->transformUsing($value);
        return $value;
    }
}