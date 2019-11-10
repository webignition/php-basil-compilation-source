<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\LineInterface;

class DocBlock extends AbstractBlock implements BlockInterface
{
    protected function canLineBeAdded(LineInterface $line): bool
    {
        return $line instanceof Comment || $line instanceof EmptyLine;
    }
}
