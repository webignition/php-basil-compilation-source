<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface SourceInterface
{
    /**
     * @return string[]
     */
    public function getStatements(): array;
    public function getMetadata(): MetadataInterface;

    public function toCode(): string;
    public function __toString(): string;
}
