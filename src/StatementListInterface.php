<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementListInterface extends SourceInterface
{
    public function mutateStatement(int $index, callable $mutator);
    public function replaceStatement(int $index, StatementInterface $statement);
    public function getStatement(int $index): ?StatementInterface;
    public function getLastStatement(): ?StatementInterface;
    public function mutateLastStatement(callable $mutator);
    public function addClassDependencies(int $index, ClassDependencyCollection $classDependencies);
    public function addVariableDependencies(int $index, VariablePlaceholderCollection $variableDependencies);
    public function addVariableExports(int $index, VariablePlaceholderCollection $variableExports);
    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies);
    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports);
}
