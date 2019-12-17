<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\MutableBlockInterface;

interface CodeBlockInterface extends BlockInterface, MutableBlockInterface
{
    public function getMetadata(): MetadataInterface;
    public function addLinesFromBlock(BlockInterface $block): void;
}
