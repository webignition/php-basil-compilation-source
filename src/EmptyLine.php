<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class EmptyLine extends AbstractLine implements LineInterface
{
    public function __construct()
    {
        parent::__construct('');
    }

    public function isStatement(): bool
    {
        return false;
    }

    public function isComment(): bool
    {
        return false;
    }

    public function isEmpty(): bool
    {
        return true;
    }
}
