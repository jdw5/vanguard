<?php

namespace Jdw5\Vanguard\Columns;

use Closure;
use Jdw5\Vanguard\Columns\Enums\Breakpoint;

class ActionColumn extends BaseColumn
{

    public function __construct(
        string|Closure $label,
        bool $srOnly = false,
        Breakpoint|string $breakpoint = Breakpoint::NONE,
    ) {
        parent::__construct($label, $srOnly, $breakpoint);
        $this->setType('col:action');
    }
    
    public function make(
        string|Closure $label,
        bool $srOnly = false,
        Breakpoint|string $breakpoint = Breakpoint::NONE,
    ): static {
        return new static($label, $srOnly, $breakpoint);
    }

    public function apply(mixed $value): void
    {
        return;
    }
}