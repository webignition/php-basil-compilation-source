<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\SourceInterface;

interface BlockInterface extends SourceInterface
{
    public function addLine(LineInterface $statement);
    public function addLinesFromBlock(BlockInterface $block);

    /**
     * @return LineInterface[]
     */
    public function getLines(): array;
}
