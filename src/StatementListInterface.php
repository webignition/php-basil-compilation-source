<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementListInterface extends SourceInterface
{
    /**
     * @return StatementInterface[]
     */
    public function getStatementObjects(): array;
    public function prependStatement(int $index, string $content);
    public function appendStatement(int $index, string $content);
    public function mutateStatement(int $index, callable $mutator);
    public function replaceStatement(int $index, StatementInterface $statement);
    public function replaceLastStatement(StatementInterface $statement);
    public function getStatement(int $index): ?StatementInterface;
    public function getLastStatement(): ?StatementInterface;
    public function prependLastStatement(string $content);
    public function appendLastStatement(string $content);
    public function mutateLastStatement(callable $mutator);
    public function addClassDependencies(int $index, ClassDependencyCollection $classDependencies);
    public function addVariableDependencies(int $index, VariablePlaceholderCollection $variableDependencies);
    public function addVariableExports(int $index, VariablePlaceholderCollection $variableExports);
    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies);
    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports);
}
