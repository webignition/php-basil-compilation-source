<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line\MethodInvocation;

use webignition\BasilCompilationSource\Line\HasMetadataInterface;
use webignition\BasilCompilationSource\Line\LineInterface;

interface MethodInvocationInterface extends LineInterface, HasMetadataInterface
{
    public function getMethodName(): string;

    /**
     * @return string[]
     */
    public function getArguments(): array;

    public function getArgumentFormat(): int;
}
