<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\MethodDefinition;

use webignition\BasilCompilationSource\Block\BlockInterface;
use webignition\BasilCompilationSource\Block\DocBlock;

interface MethodDefinitionInterface extends BlockInterface
{
    public function isPublic(): bool;
    public function isProtected(): bool;
    public function isPrivate(): bool;
    public function getName(): string;
    public function getArguments(): array;
    public function getReturnType(): ?string;
    public function isStatic(): bool;
    public function getVisibility(): string;
    public function setDocBlock(DocBlock $docBlock);
    public function getDocBlock(): DocBlock;
}
