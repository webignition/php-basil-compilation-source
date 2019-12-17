<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\StatementInterface;

class IndexedStatement
{
    private $statement;
    private $index;

    public function __construct(StatementInterface $statement, int $index)
    {
        $this->statement = $statement;
        $this->index = $index;
    }

    public function getStatement(): StatementInterface
    {
        return $this->statement;
    }

    public function getIndex(): int
    {
        return $this->index;
    }
}
