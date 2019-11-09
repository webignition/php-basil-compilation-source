<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

class EmptyLine extends AbstractLine implements LineInterface
{
    public function __construct()
    {
        parent::__construct('', LineTypes::EMPTY);
    }
}
