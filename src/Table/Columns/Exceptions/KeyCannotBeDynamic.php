<?php

namespace Jdw5\Vanguard\Table\Columns\Exceptions;

class KeyCannotBeDynamic extends \Exception
{
    public static function make(): self
    {
        return new self('The key column cannot be preferenced.');
    }
}