<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementInterface
{
    public function getContent(): string;
    public function getMetadata(): MetadataInterface;
    public function prepend(string $content);
    public function append(string $content);
    public function mutate(callable $mutator);
    public function addClassDependencies(ClassDependencyCollection $classDependencies);
    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies);
    public function addVariableExports(VariablePlaceholderCollection $variableExports);
    public function __toString(): string;
}
