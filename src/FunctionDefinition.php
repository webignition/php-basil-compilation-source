<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class FunctionDefinition implements FunctionDefinitionInterface
{
    private $name;
    private $arguments = [];
    private $content;

    public function __construct(string $name, SourceInterface $content, ?array $arguments = null)
    {
        $this->name = $name;
        $this->content = $content;
        $this->arguments = $arguments ?? [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): SourceInterface
    {
        return $this->content;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function addLine(LineInterface $statement)
    {
        $this->content->addLine($statement);
    }

    public function addLines(array $statements)
    {
        $this->content->addLines($statements);
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        return $this->content->getLines();
    }

    /**
     * @return LineInterface[]
     */
    public function getLineObjects(): array
    {
        return $this->content->getLineObjects();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->content->getMetadata();
    }

    public function mutateLastStatement(callable $mutator)
    {
        $this->content->mutateLastStatement($mutator);
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies)
    {
        $this->content->addClassDependenciesToLastStatement($classDependencies);
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        $this->content->addVariableDependenciesToLastStatement($variableDependencies);
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $this->content->addVariableExportsToLastStatement($variableExports);
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'function',
            'name' => $this->name,
            'content' => $this->content->jsonSerialize(),
        ];
    }
}
