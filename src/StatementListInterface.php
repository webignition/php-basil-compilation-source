<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementListInterface extends SourceInterface
{
    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies);
    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports);
}
