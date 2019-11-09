<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

abstract class AbstractStatement extends AbstractLine implements StatementInterface
{
    public function __construct(string $content, string $type, ?MetadataInterface $metadata = null)
    {
        parent::__construct($content, $type, $metadata);
    }

    public function prepend(string $content)
    {
        $this->mutate(function (string $statement) use ($content) {
            return $content . $statement;
        });
    }

    public function append(string $content)
    {
        $this->mutate(function (string $statement) use ($content) {
            return $statement . $content;
        });
    }

    public function mutate(callable $mutator)
    {
        $this->content = $mutator($this->content);
    }

    public function addClassDependencies(ClassDependencyCollection $classDependencies)
    {
        $this->getMetadata()->addClassDependencies($classDependencies);
    }

    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies)
    {
        $this->getMetadata()->addVariableDependencies($variableDependencies);
    }

    public function addVariableExports(VariablePlaceholderCollection $variableExports)
    {
        $this->getMetadata()->addVariableExports($variableExports);
    }

    public function mutateLastStatement(callable $mutator)
    {
        return $this->mutate($mutator);
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies)
    {
        return $this->addClassDependencies($classDependencies);
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        return $this->addVariableDependencies($variableDependencies);
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $this->addVariableExports($variableExports);
    }
}