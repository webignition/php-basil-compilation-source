<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementListInterface extends SourceInterface
{
    /**
     * @return StatementInterface[]
     */
    public function getStatementObjects(): array;
    public function prependStatement(int $index, string $content);
    public function appendStatement(int $index, string $content);
    public function mutateStatement(int $index, callable $mutator);
    public function replaceStatement(int $index, StatementInterface $statement);
    public function replaceLastStatement(StatementInterface $statement);
    public function getStatement(int $index): ?StatementInterface;
    public function getLastStatement(): ?StatementInterface;
}
