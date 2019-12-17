<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\LineInterface;

interface BlockInterface
{
    public function addLine(LineInterface $statement): void;

    /**
     * @return LineInterface[]
     */
    public function getLines(): array;
}
