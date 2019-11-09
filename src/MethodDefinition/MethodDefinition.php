<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\MethodDefinition;

use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Block\Block;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\MutableBlockInterface;
use webignition\BasilCompilationSource\SourceInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class MethodDefinition implements MethodDefinitionInterface, MutableBlockInterface
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PROTECTED = 'protected';
    public const VISIBILITY_PRIVATE = 'private';

    private $visibility = self::VISIBILITY_PUBLIC;
    private $returnType = null;
    private $name;
    private $lineList;
    private $arguments = [];
    private $isStatic = false;

    public function __construct(string $name, Block $lineList, ?array $arguments = null)
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
     * @return \webignition\BasilCompilationSource\Line\LineInterface[]
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

    public function setPublic()
    {
        $this->visibility = self::VISIBILITY_PUBLIC;
    }

    public function setProtected()
    {
        $this->visibility = self::VISIBILITY_PROTECTED;
    }

    public function setPrivate()
    {
        $this->visibility = self::VISIBILITY_PRIVATE;
    }

    public function isPublic(): bool
    {
        return self::VISIBILITY_PUBLIC === $this->visibility;
    }

    public function isProtected(): bool
    {
        return self::VISIBILITY_PROTECTED === $this->visibility;
    }

    public function isPrivate(): bool
    {
        return self::VISIBILITY_PRIVATE === $this->visibility;
    }

    public function getReturnType(): ?string
    {
        return $this->returnType;
    }

    public function setReturnType(string $returnType)
    {
        $this->returnType = $returnType;
    }

    public function setStatic(): void
    {
        $this->isStatic = true;
    }

    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
