<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface ClassDefinitionInterface extends SourceInterface
{
    public function getName(): string;

    /**
     * @return FunctionDefinitionInterface[]
     */
    public function getFunctions(): array;
}
