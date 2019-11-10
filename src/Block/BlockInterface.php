<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;

interface BlockInterface
{
    public function getMetadata(): MetadataInterface;
    public function addLine(LineInterface $statement);
    public function addLinesFromBlock(BlockInterface $block);

    /**
     * @return LineInterface[]
     */
    public function getLines(): array;
}
