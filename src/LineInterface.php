<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface LineInterface
{
    public function getContent(): string;
    public function isStatement(): bool;
    public function isComment(): bool;
    public function isEmpty(): bool;
    public function __toString(): string;
}
