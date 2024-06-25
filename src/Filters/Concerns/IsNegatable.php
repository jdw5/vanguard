<?php

namespace Jdw5\Vanguard\Filters\Concerns;

trait IsNegatable
{
    protected bool $negated = false;

    public function not(): static
    {
        $this->setNegation(true);
        return $this;
    }

    public function negate(): static
    {
        return $this->not();
    }

    protected function setNegation(bool $negation): void
    {
        if (is_null($this->getRestrictions())) return;
        $this->negated = $negation;
    }

    public function getNegation(): bool
    {
        return $this->evaluate($this->negated);
    }

    public function isNegated(): bool
    {
        return $this->getNegation();
    }
}