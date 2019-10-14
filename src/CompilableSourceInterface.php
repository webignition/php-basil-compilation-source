<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface CompilableSourceInterface
{
    public function addPredecessor(CompilableSourceInterface $predecessor);

    /**
     * @return string[]
     */
    public function getStatements(): array;

    public function getCompilationMetadata(): CompilationMetadataInterface;
    public function withCompilationMetadata(
        CompilationMetadataInterface $compilationMetadata
    ): CompilableSourceInterface;

    public function mergeCompilationData(array $compilationDataCollection): CompilableSourceInterface;

    public function __toString(): string;
}
