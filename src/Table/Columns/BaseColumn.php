<?php

namespace Jdw5\Vanguard\Table\Columns;

use Jdw5\Vanguard\Concerns\HasBreakpoints;
use Jdw5\Vanguard\Primitive;
use Jdw5\Vanguard\Concerns\HasName;
use Jdw5\Vanguard\Concerns\HasType;
use Jdw5\Vanguard\Concerns\HasLabel;
use Jdw5\Vanguard\Concerns\IsHideable;
use Jdw5\Vanguard\Concerns\HasMetadata;
use Jdw5\Vanguard\Concerns\IsIncludable;
use Jdw5\Vanguard\Table\Columns\Concerns\IsKey;
use Jdw5\Vanguard\Table\Columns\Concerns\HasSort;
use Jdw5\Vanguard\Table\Columns\Concerns\HasFallback;
use Jdw5\Vanguard\Table\Columns\Concerns\HasTransform;
use Jdw5\Vanguard\Table\Columns\Concerns\IsPreferable;
use Jdw5\Vanguard\Table\Columns\Exceptions\ReservedColumnName;

/**
 * Class BaseColumn
 * 
 * Defines the required properties for a column
 * 
 */
abstract class BaseColumn extends Primitive
{
    use HasName;
    use HasLabel;
    use HasMetadata;
    use HasFallback;
    use HasTransform;
    use HasSort;
    use IsIncludable;
    use IsHideable;
    use HasBreakpoints;
    use IsKey;
    use IsPreferable;

    const RESERVED = [
        'key',
        'actions',
    ];

    public function __construct(string $name)
    {
        if (in_array($name, static::RESERVED)) {
            throw ReservedColumnName::make($name);
        }
        $this->setName($name);
        $this->setLabel($this->labelise($this->getName()));
        $this->setUp();
    }

    /**
     * Statically create the column
     */
    public static function make(string $name): static
    {
        return new static($name);
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
            'hidden' => $this->isHidden(),
            'breakpoint' => $this->getBreakpoint(),
            'sr_only' => $this->isSrOnly(),

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
}