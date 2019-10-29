<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface FunctionDefinitionInterface extends LineListInterface
{
    public function getName(): string;
    public function getArguments(): array;
}
