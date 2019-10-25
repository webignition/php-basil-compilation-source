<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface FunctionDefinitionInterface extends SourceInterface
{
    public function getName(): string;
    public function getContent(): SourceInterface;
    public function getArguments(): array;
}
