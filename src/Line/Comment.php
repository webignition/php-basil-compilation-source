<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

class Comment extends AbstractLine
{
    public function __construct(string $content)
    {
        parent::__construct($content, LineTypes::COMMENT);
    }
}
