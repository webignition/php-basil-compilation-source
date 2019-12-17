<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class Statement extends AbstractLine implements StatementInterface
{
    private $metadata;

    public function __construct(string $content, ?MetadataInterface $metadata = null)
    {
        parent::__construct($content, LineTypes::STATEMENT);

        $this->metadata = $metadata ?? new Metadata();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function prepend(string $content): void
    {
        $this->mutate(function (string $statement) use ($content) {
            return $content . $statement;
        });
    }

    public function append(string $content): void
    {
        $this->mutate(function (string $statement) use ($content) {
            return $statement . $content;
        });
    }

    public function mutate(callable $mutator): void
    {
        $this->setContent($mutator($this->getContent()));
    }

    public function addClassDependencies(ClassDependencyCollection $classDependencies): void
    {
        $this->getMetadata()->addClassDependencies($classDependencies);
    }

    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies): void
    {
        $this->getMetadata()->addVariableDependencies($variableDependencies);
    }

    public function addVariableExports(VariablePlaceholderCollection $variableExports): void
    {
        $this->getMetadata()->addVariableExports($variableExports);
    }

    public function mutateLastStatement(callable $mutator): void
    {
        $this->mutate($mutator);
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies): void
    {
        $this->addClassDependencies($classDependencies);
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies): void
    {
        $this->addVariableDependencies($variableDependencies);
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports): void
    {
        $this->addVariableExports($variableExports);
    }
}
