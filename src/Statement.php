<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class Statement implements StatementInterface
{
    private $content;
    private $metadata;

    public function __construct(string $content, ?MetadataInterface $metadata = null)
    {
        $this->content = $content;
        $this->metadata = $metadata ?? new Metadata();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string[]
     */
    public function getStatements(): array
    {
        return [$this->content];
    }

    public function getStatementObjects(): array
    {
        return [$this];
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function prepend(string $content): StatementInterface
    {
        return $this->mutate(function (string $statement) use ($content) {
            return $content . $statement;
        });
    }

    public function append(string $content): StatementInterface
    {
        return $this->mutate(function (string $statement) use ($content) {
            return $statement . $content;
        });
    }

    public function mutate(callable $mutator): StatementInterface
    {
        $new = clone $this;
        $new->content = $mutator($this->content);

        return $new;
    }

    public function mutateLastStatement(callable $mutator)
    {
        return $this->mutate($mutator);
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies)
    {
        $this->metadata->addClassDependencies($classDependencies);
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        $this->metadata->addVariableDependencies($variableDependencies);
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $this->metadata->addVariableExports($variableExports);
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
