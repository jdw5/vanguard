<?php

namespace Jdw5\Vanguard\Table\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Tables
{
    public static function make(?Builder $data = null): static;
}