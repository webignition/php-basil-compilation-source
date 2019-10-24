<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface StatementInterface extends SourceInterface
{
    public function getContent(): string;
}
