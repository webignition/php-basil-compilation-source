<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\ClassDefinition;

use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\MethodDefinition\MethodDefinitionInterface;

interface ClassDefinitionInterface
{
    public function getMetadata(): MetadataInterface;
    public function getName(): string;

    /**
     * @return MethodDefinitionInterface[]
     */
    public function getMethods(): array;

    public function getMethod(string $name): ?MethodDefinitionInterface;
}
