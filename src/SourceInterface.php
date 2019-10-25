<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface SourceInterface
{
    public function addStatement(StatementInterface $statement);

    /**
     * @param StatementInterface[] $statements
     */
    public function addStatements(array $statements);

    /**
     * @return string[]
     */
    public function getStatements(): array;

    /**
     * @return StatementInterface[]
     */
    public function getStatementObjects(): array;

    public function getMetadata(): MetadataInterface;

    public function mutateLastStatement(callable $mutator);
    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies);
    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports);
}
