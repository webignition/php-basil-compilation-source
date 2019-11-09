<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\MutableBlockInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

interface StatementInterface extends LineInterface, MutableBlockInterface
{
    public function prepend(string $content);
    public function append(string $content);
    public function mutate(callable $mutator);
    public function addClassDependencies(ClassDependencyCollection $classDependencies);
    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExports(VariablePlaceholderCollection $variableExports);
}
