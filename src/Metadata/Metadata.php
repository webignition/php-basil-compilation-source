<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Metadata;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class Metadata implements MetadataInterface
{
    private $classDependencies;
    private $variableDependencies;
    private $variableExports;

    public function __construct()
    {
        $this->classDependencies = new ClassDependencyCollection();
        $this->variableDependencies = new VariablePlaceholderCollection();
        $this->variableExports = new VariablePlaceholderCollection();
    }

    public function getClassDependencies(): ClassDependencyCollection
    {
        return $this->classDependencies;
    }

    public function getVariableExports(): VariablePlaceholderCollection
    {
        return $this->variableExports;
    }

    public function getVariableDependencies(): VariablePlaceholderCollection
    {
        return $this->variableDependencies;
    }

    public function withClassDependencies(ClassDependencyCollection $classDependencies): MetadataInterface
    {
        $new = clone $this;
        $new->classDependencies = $classDependencies;

        return $new;
    }

    public function withVariableDependencies(
        VariablePlaceholderCollection $variableDependencies
    ): MetadataInterface {
        $new = clone $this;
        $new->variableDependencies = $variableDependencies;

        return $new;
    }

    public function withVariableExports(VariablePlaceholderCollection $variableExports): MetadataInterface
    {
        $new = clone $this;
        $new->variableExports = $variableExports;

        return $new;
    }

    public function addClassDependencies(ClassDependencyCollection $classDependencies)
    {
        foreach ($classDependencies->getLines() as $classDependency) {
            $this->classDependencies->addLine($classDependency);
        }
    }

    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies)
    {
        $this->variableDependencies = $this->variableDependencies->merge([$variableDependencies]);
    }

    public function addVariableExports(VariablePlaceholderCollection $variableExports)
    {
        $this->variableExports = $this->variableExports->merge([$variableExports]);
    }

    public function add(MetadataInterface $metadata)
    {
        $this->addClassDependencies($metadata->getClassDependencies());
        $this->addVariableDependencies($metadata->getVariableDependencies());
        $this->addVariableExports($metadata->getVariableExports());
    }
}
