<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line\MethodInvocation;

use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;

interface MethodInvocationInterface extends LineInterface
{
    public function getMethodName(): string;

    /**
     * @return string[]
     */
    public function getArguments(): array;

    public function getArgumentFormat(): int;
    public function getMetadata(): MetadataInterface;
}
