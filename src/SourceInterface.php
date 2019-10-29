<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface SourceInterface extends \JsonSerializable
{
    public function addLine(LineInterface $statement);

    /**
     * @param LineInterface[] $statements
     */
    public function addLines(array $statements);

    /**
     * @return string[]
     */
    public function getLines(): array;

    /**
     * @return LineInterface[]
     */
    public function getLineObjects(): array;

    public function getMetadata(): MetadataInterface;

    public function mutateLastStatement(callable $mutator);
    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies);
    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports);
    public function getContent(): array;
}
