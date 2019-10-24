<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface MetadataInterface
{
    public function getClassDependencies(): ClassDependencyCollection;
    public function getVariableExports(): VariablePlaceholderCollection;
    public function getVariableDependencies(): VariablePlaceholderCollection;

    public function withClassDependencies(ClassDependencyCollection $classDependencies): MetadataInterface;
    public function withVariableDependencies(VariablePlaceholderCollection $variableDependencies): MetadataInterface;
    public function withVariableExports(VariablePlaceholderCollection $variableExports): MetadataInterface;

    public function addClassDependencies(ClassDependencyCollection $classDependencies);
    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExports(VariablePlaceholderCollection $variableExports);

    public function add(MetadataInterface $metadata);
}
