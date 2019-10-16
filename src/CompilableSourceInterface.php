<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

use webignition\BasilCompilationSource\CompilationMetadataInterface as Metadata;

interface CompilableSourceInterface
{
    /**
     * @return string[]
     */
    public function getStatements(): array;

    public function getCompilationMetadata(): Metadata;

    /**
     * @return CompilableSourceInterface[]
     */
    public function getPredecessors(): array;

    /**
     * @param CompilableSourceInterface[] $predecessors
     *
     * @return CompilableSourceInterface
     */
    public function withPredecessors(array $predecessors): CompilableSourceInterface;

    /**
     * @param string[] $statements
     *
     * @return CompilableSourceInterface
     */
    public function withStatements(array $statements): CompilableSourceInterface;

    public function withCompilationMetadata(Metadata $compilationMetadata): CompilableSourceInterface;

    public function prependStatement(int $index, string $content);
    public function appendStatement(int $index, string $content);

    public function __toString(): string;
}
