<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class FunctionDefinition implements FunctionDefinitionInterface, MutableListLineListInterface
{
    private $name;
    private $lineList;
    private $arguments = [];

    public function __construct(string $name, LineList $lineList, ?array $arguments = null)
    {
        $this->name = $name;
        $this->lineList = $lineList;
        $this->arguments = $arguments ?? [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return SourceInterface[]
     */
    public function getSources(): array
    {
        return $this->lineList->getSources();
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function addLine(LineInterface $statement)
    {
        $this->lineList->addLine($statement);
    }

    public function addLinesFromSource(SourceInterface $source)
    {
        $this->lineList->addLinesFromSource($source);
    }

    public function addLinesFromSources(array $sources)
    {
        $this->lineList->addLinesFromSources($sources);
    }

    /**
     * @return LineInterface[]
     */
    public function getLines(): array
    {
        return $this->lineList->getLines();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->lineList->getMetadata();
    }

    public function mutateLastStatement(callable $mutator)
    {
        $this->lineList->mutateLastStatement($mutator);
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies)
    {
        $this->lineList->addClassDependenciesToLastStatement($classDependencies);
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        $this->lineList->addVariableDependenciesToLastStatement($variableDependencies);
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $this->lineList->addVariableExportsToLastStatement($variableExports);
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'function',
            'name' => $this->name,
            'line-list' => $this->lineList->jsonSerialize(),
        ];
    }
}
