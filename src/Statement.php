<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class Statement extends AbstractLine implements StatementInterface
{
    private $metadata;

    public function __construct(string $content, ?MetadataInterface $metadata = null)
    {
        parent::__construct($content);

        $this->metadata = $metadata ?? new Metadata();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
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
        $this->metadata->addClassDependencies($classDependencies);
    }

    public function addVariableDependencies(VariablePlaceholderCollection $variableDependencies)
    {
        $this->metadata->addVariableDependencies($variableDependencies);
    }

    public function addVariableExports(VariablePlaceholderCollection $variableExports)
    {
        $this->metadata->addVariableExports($variableExports);
    }

    public function isStatement(): bool
    {
        return true;
    }

    public function isComment(): bool
    {
        return false;
    }

    public function isEmpty(): bool
    {
        return false;
    }
}
