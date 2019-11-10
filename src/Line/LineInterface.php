<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

interface LineInterface
{
    public function getContent(): string;
    public function getType(): string;
    public function __toString(): string;
}
