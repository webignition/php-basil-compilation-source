<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface MethodDefinitionInterface extends LineListInterface
{
    public function isPublic(): bool;
    public function isProtected(): bool;
    public function isPrivate(): bool;
    public function getName(): string;
    public function getArguments(): array;
    public function getReturnType(): ?string;
    public function isStatic(): bool;
    public function getVisibility(): string;
}
