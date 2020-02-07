<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

class LineTypes
{
    public const STATEMENT = 'statement';
    public const USE_STATEMENT = 'use-statement';
    public const COMMENT = 'comment';
    public const EMPTY = 'empty';
    public const METHOD_INVOCATION = 'method-invocation';
}
