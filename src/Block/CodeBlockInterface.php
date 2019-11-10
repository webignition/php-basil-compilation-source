<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Metadata\MetadataInterface;

interface CodeBlockInterface extends BlockInterface
{
    public function getMetadata(): MetadataInterface;
    public function addLinesFromBlock(BlockInterface $block);
}
