<?php

namespace Jdw5\Vanguard\Pagination\Enums;

enum PaginationType: string
{
    case NONE = null;
    case SIMPLE = 'simple';
    case CURSOR = 'cursor';
}