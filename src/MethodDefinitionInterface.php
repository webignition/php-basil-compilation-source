<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface MethodDefinitionInterface extends LineListInterface
{
    public function getVisibility(): string;
    public function getName(): string;
    public function getArguments(): array;
}
