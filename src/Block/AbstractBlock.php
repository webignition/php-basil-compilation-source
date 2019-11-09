<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\SourceInterface;

abstract class AbstractBlock implements BlockInterface
{
    /**
     * @var LineInterface[]
     */
    protected $lines = [];

    public function __construct(array $sources = [])
    {
        $this->addLinesFromSources($sources);
    }

    abstract protected function canLineBeAdded(LineInterface $line): bool;

    public function addLine(LineInterface $line)
    {
        if ($this->canLineBeAdded($line)) {
            $this->lines[] = $line;
        }
    }

    public function addLinesFromSource(SourceInterface $source)
    {
        foreach ($source->getSources() as $line) {
            if ($line instanceof LineInterface) {
                $this->addLine($line);
            }
        }
    }

    public function addLinesFromSources(array $sources)
    {
        foreach ($sources as $source) {
            if ($source instanceof SourceInterface) {
                $this->addLinesFromSource($source);
            }
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
