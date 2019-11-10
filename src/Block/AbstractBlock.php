<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;

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

            if ($source instanceof BlockInterface) {
                $this->addLinesFromBlock($source);
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

    public function addLinesFromBlock(BlockInterface $block)
    {
        foreach ($block->getLines() as $line) {
            $this->addLine($line);
        }
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->lines as $line) {
            if ($line instanceof LineInterface) {
                $metadata->add($line->getMetadata());
            }
        }

        return $metadata;
    }

    /**
     * @return LineInterface[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getSources(): array
    {
        return $this->getLines();
    }
}
