<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;

interface MutableBlockInterface
{
    public function mutateLastStatement(callable $mutator): void;
    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies): void;
    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies): void;
    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports): void;
}
