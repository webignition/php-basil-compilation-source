<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Metadata;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

interface MetadataInterface
{
    public function getClassDependencies(): ClassDependencyCollection;
    public function getVariableExports(): VariablePlaceholderCollection;
    public function getVariableDependencies(): VariablePlaceholderCollection;

    public function withClassDependencies(ClassDependencyCollection $classDependencies): MetadataInterface;
    public function withVariableDependencies(VariablePlaceholderCollection $variableDependencies): MetadataInterface;
    public function withVariableExports(VariablePlaceholderCollection $variableExports): MetadataInterface;

    public function addClassDependencies(ClassDependencyCollection $classDependencies): void;
    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies): void;
    public function addVariableExports(VariablePlaceholderCollection $variableExports): void;

    public function add(MetadataInterface $metadata): void;
}
