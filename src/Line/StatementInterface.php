<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\MutableBlockInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

interface StatementInterface extends LineInterface, MutableBlockInterface, HasMetadataInterface
{
    public function prepend(string $content): void;
    public function append(string $content): void;
    public function mutate(callable $mutator): void;
    public function addClassDependencies(ClassDependencyCollection $classDependencies): void;
    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies): void;
    public function addVariableExports(VariablePlaceholderCollection $variableExports): void;
}
