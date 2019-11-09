<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

use webignition\BasilCompilationSource\SourceInterface;

interface LineInterface extends SourceInterface
{
    public function getContent(): string;
    public function getType(): string;
    public function __toString(): string;
}
