<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\ClassDefinition;

use webignition\BasilCompilationSource\MethodDefinitionInterface;
use webignition\BasilCompilationSource\SourceInterface;

interface ClassDefinitionInterface extends SourceInterface
{
    public function getName(): string;

    /**
     * @return MethodDefinitionInterface[]
     */
    public function getMethods(): array;

    public function getMethod(string $name): ?MethodDefinitionInterface;
}
