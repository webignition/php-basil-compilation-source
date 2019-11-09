<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface LineListInterface extends SourceInterface
{
    public function addLine(LineInterface $statement);
    public function addLinesFromSource(SourceInterface $source);

    /**
     * @param SourceInterface[] $sources
     */
    public function addLinesFromSources(array $sources);

    /**
     * @return LineInterface[]
     */
    public function getLines(): array;
}
