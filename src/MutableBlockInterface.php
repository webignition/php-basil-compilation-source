<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface MutableBlockInterface extends SourceInterface
{
    public function mutateLastStatement(callable $mutator);
    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies);
    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports);
}
