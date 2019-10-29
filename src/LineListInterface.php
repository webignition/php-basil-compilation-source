<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface LineListInterface extends SourceInterface
{
    public function addLine(LineInterface $statement);

    /**
     * @param LineInterface[] $statements
     */
    public function addLines(array $statements);

    /**
     * @return LineInterface[]
     */
    public function getLines(): array;

    public function mutateLastStatement(callable $mutator);
    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies);
    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports);
}
