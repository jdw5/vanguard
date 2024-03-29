<?php

namespace Jdw5\Vanguard\Table;

use Illuminate\Database\Eloquent\Builder;
use Jdw5\Vanguard\Primitive;
use Jdw5\Vanguard\Concerns\HasId;
use Jdw5\Vanguard\Concerns\HasActions;
use Jdw5\Vanguard\Table\Concerns\HasKey;
use Jdw5\Vanguard\Table\Contracts\Tables;
use Jdw5\Vanguard\Concerns\HasRefinements;
use Jdw5\Vanguard\Table\Concerns\HasModel;
use Jdw5\Vanguard\Table\Concerns\HasColumns;
use Jdw5\Vanguard\Table\Concerns\HasDynamicPagination;
use Jdw5\Vanguard\Table\Concerns\HasRecords;
use Jdw5\Vanguard\Table\Concerns\HasPagination;
use Jdw5\Vanguard\Table\Concerns\HasDynamics;
use Jdw5\Vanguard\Table\Exceptions\InvalidKeyException;

abstract class Table extends Primitive implements Tables
{
    use HasColumns;
    use HasActions;
    use HasId;
    use HasModel;
    use HasPagination;
    use HasRecords;
    use HasRefinements;
    use HasKey;
    use HasDynamics;
    use HasDynamicPagination;

    public function __construct(?Builder $data = null)
    {
        $this->query($data);
    }

    /**
     * Create a new table instance.
     * 
     * @param Builder|null $data
     * @return static
     */
    public static function make(?Builder $data = null): static
    {
        return new static($data);
    }

    /**
     * Get the key for the table.
     * 
     * @return string
     * @throws InvalidKeyException
     * @return string
     */
    public function tableKey(): string 
    {
        try { 
            return $this->getKey();
        } catch (InvalidKeyException $e) {
            return $this->findKeyColumn()?->getName() ?? throw $e;
        }
    }

    /**
     * Serialize the table to JSON.
     * 
     * @return array
     */
    public function jsonSerialize(): array
    {
        $show = $this->hasDynamicPagination() ? ['show' => $this->getActivePagination()] : [];

        return array_merge([
            'meta' => $this->getMeta(),
            'rows' => $this->getRecords(),
            'cols' => $this->getTableColumns()->values(),
            'refinements' => [
                'sorts' => $this->getSorts()->values(),
                'filters' => $this->getFilters()->values(),
            ],
            'actions' => [
                'inline' => $this->getInlineActions()->values(),
                'bulk' => $this->getBulkActions()->values(),
                'page' => $this->getPageActions()->values(),
                'default' => $this->getDefaultAction(),
            ],
            'recordKey' => $this->tableKey(),
        ], $show);
    }   
}