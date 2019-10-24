<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementInterface extends SourceInterface
{
    public function getContent(): string;
    public function prepend(string $content): StatementInterface;
    public function append(string $content): StatementInterface;
    public function mutate(callable $mutator): StatementInterface;
}
