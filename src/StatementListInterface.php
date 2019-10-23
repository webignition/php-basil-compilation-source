<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementListInterface extends SourceInterface
{
    /**
     * @return StatementListInterface[]
     */
    public function getPredecessors(): array;

    /**
     * @param StatementListInterface[] $predecessors
     *
     * @return StatementListInterface
     */
    public function withPredecessors(array $predecessors): StatementListInterface;

    /**
     * @param string[] $statements
     *
     * @return StatementListInterface
     */
    public function withStatements(array $statements): StatementListInterface;

    public function withMetadata(MetadataInterface $metadata): StatementListInterface;

    public function prependStatement(int $index, string $content);
    public function appendStatement(int $index, string $content);
    public function mutateStatement(int $index, callable $mutator);
}
