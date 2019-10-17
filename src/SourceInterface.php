<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface SourceInterface
{
    /**
     * @return string[]
     */
    public function getStatements(): array;

    public function getMetadata(): MetadataInterface;

    /**
     * @return SourceInterface[]
     */
    public function getPredecessors(): array;

    /**
     * @param SourceInterface[] $predecessors
     *
     * @return SourceInterface
     */
    public function withPredecessors(array $predecessors): SourceInterface;

    /**
     * @param string[] $statements
     *
     * @return SourceInterface
     */
    public function withStatements(array $statements): SourceInterface;

    public function withMetadata(MetadataInterface $metadata): SourceInterface;

    public function prependStatement(int $index, string $content);
    public function appendStatement(int $index, string $content);
    public function mutateStatement(int $index, callable $mutator);

    public function __toString(): string;
}
