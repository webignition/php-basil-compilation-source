<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface MetadataInterface
{
    public function getClassDependencies(): ClassDependencyCollection;
    public function getVariableExports(): VariablePlaceholderCollection;
    public function getVariableDependencies(): VariablePlaceholderCollection;

    public function withClassDependencies(ClassDependencyCollection $classDependencies): MetadataInterface;
    public function withVariableDependencies(
        VariablePlaceholderCollection $variableDependencies
    ): MetadataInterface;
    public function withVariableExports(VariablePlaceholderCollection $variableExports): MetadataInterface;

    public function withAdditionalClassDependencies(
        ClassDependencyCollection $classDependencies
    ): MetadataInterface;

    public function withAdditionalVariableDependencies(
        VariablePlaceholderCollection $variableDependencies
    ): MetadataInterface;

    public function withAdditionalVariableExports(
        VariablePlaceholderCollection $variableExports
    ): MetadataInterface;

    /**
     * @param MetadataInterface[] $metadataCollection
     *
     * @return MetadataInterface
     */
    public function merge(array $metadataCollection): MetadataInterface;
}
