<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementInterface extends SourceInterface
{
    public function getContent(): string;
    public function prepend(string $content);
    public function append(string $content);
    public function mutate(callable $mutator);
    public function __toString(): string;
}
