<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\MethodDefinition;

use webignition\BasilCompilationSource\Block\BlockInterface;
use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\Block\DocBlock;
use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Block\Block;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\MutableBlockInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class MethodDefinition implements MethodDefinitionInterface, MutableBlockInterface
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PROTECTED = 'protected';
    public const VISIBILITY_PRIVATE = 'private';

    private $visibility = self::VISIBILITY_PUBLIC;
    private $returnType = null;
    private $name;
    private $block;
    private $arguments = [];
    private $isStatic = false;
    private $docBlock;

    public function __construct(string $name, Block $block, ?array $arguments = null)
    {
        $this->name = $name;
        $this->block = $block;
        $this->arguments = $arguments ?? [];
        $this->docBlock = new DocBlock();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function addLine(LineInterface $statement)
    {
        $this->block->addLine($statement);
    }

    public function addLinesFromBlock(BlockInterface $block)
    {
        $this->block->addLinesFromBlock($block);
    }

    /**
     * @return LineInterface[]
     */
    public function getLines(): array
    {
        return $this->block->getLines();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->block->getMetadata();
    }

    public function mutateLastStatement(callable $mutator)
    {
        $this->block->mutateLastStatement($mutator);
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies)
    {
        $this->block->addClassDependenciesToLastStatement($classDependencies);
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        $this->block->addVariableDependenciesToLastStatement($variableDependencies);
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $this->block->addVariableExportsToLastStatement($variableExports);
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

    public function setDocBlock(DocBlock $docBlock)
    {
        $this->docBlock = $docBlock;
    }

    public function getDocBlock(): DocBlock
    {
        return $this->docBlock;
    }
}
