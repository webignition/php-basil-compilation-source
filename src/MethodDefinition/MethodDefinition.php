<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\MethodDefinition;

use webignition\BasilCompilationSource\Block\BlockInterface;
use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\Block\CodeBlockInterface;
use webignition\BasilCompilationSource\Block\DocBlock;
use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class MethodDefinition implements MethodDefinitionInterface
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PROTECTED = 'protected';
    public const VISIBILITY_PRIVATE = 'private';

    private $visibility = self::VISIBILITY_PUBLIC;

    /**
     * @var string|null
     */
    private $returnType = null;
    private $name;
    private $block;

    /**
     * @var array<string>|null
     */
    private $arguments = [];
    private $isStatic = false;
    private $docBlock;

    /**
     * @param string $name
     * @param CodeBlockInterface $block
     * @param array<string>|null $arguments
     */
    public function __construct(string $name, CodeBlockInterface $block, ?array $arguments = null)
    {
        $this->visibility = self::VISIBILITY_PUBLIC;
        $this->returnType = null;
        $this->name = $name;
        $this->block = $block;
        $this->arguments = $arguments ?? [];
        $this->isStatic = false;
        $this->docBlock = new DocBlock();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function addLine(LineInterface $statement): void
    {
        $this->block->addLine($statement);
    }

    public function addLinesFromBlock(BlockInterface $block): void
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

    public function mutateLastStatement(callable $mutator): void
    {
        $this->block->mutateLastStatement($mutator);
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies): void
    {
        $this->block->addClassDependenciesToLastStatement($classDependencies);
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies): void
    {
        $this->block->addVariableDependenciesToLastStatement($variableDependencies);
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports): void
    {
        $this->block->addVariableExportsToLastStatement($variableExports);
    }

    public function setPublic(): void
    {
        $this->visibility = self::VISIBILITY_PUBLIC;
    }

    public function setProtected(): void
    {
        $this->visibility = self::VISIBILITY_PROTECTED;
    }

    public function setPrivate(): void
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

    public function setReturnType(string $returnType): void
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

    public function setDocBlock(DocBlock $docBlock): void
    {
        $this->docBlock = $docBlock;
    }

    public function getDocBlock(): DocBlock
    {
        return $this->docBlock;
    }
}
