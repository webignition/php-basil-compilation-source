<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface MethodDefinitionInterface extends FunctionDefinitionInterface
{
    public function getVisibility(): string;
}
