<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class EmptyLine implements LineInterface
{
    public function getContent(): string
    {
        return '';
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

    public function __toString(): string
    {
        return '';
    }
}
