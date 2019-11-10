<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\LineInterface;

abstract class AbstractBlock implements BlockInterface
{
    /**
     * @var LineInterface[]
     */
    protected $lines = [];

    public function __construct(array $sources = [])
    {
        foreach ($sources as $source) {
            if ($source instanceof LineInterface) {
                $this->addLine($source);
            }
        }
    }

    abstract protected function canLineBeAdded(LineInterface $line): bool;

    public function addLine(LineInterface $line)
    {
        if ($this->canLineBeAdded($line)) {
            $this->lines[] = $line;
        }
    }

    /**
     * @return LineInterface[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }
}
