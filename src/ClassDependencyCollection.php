<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

use webignition\BasilCompilationSource\Block\BlockInterface;
use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;

class ClassDependencyCollection extends AbstractUniqueCollection implements \Iterator, BlockInterface
{
    /**
     * @param string $id
     *
     * @return ClassDependency
     *
     * @throws UnknownItemException
     */
    public function get(string $id): ClassDependency
    {
        return parent::get($id);
    }

    /**
     * @return ClassDependency[]
     */
    public function getAll(): array
    {
        return parent::getAll();
    }

    public function withAdditionalItems(array $items): ClassDependencyCollection
    {
        return parent::withAdditionalItems($items);
    }

    public function merge(array $collections): ClassDependencyCollection
    {
        return parent::merge($collections);
    }

    protected function add($item)
    {
        if ($item instanceof ClassDependency) {
            $this->doAdd($item);
        }
    }

    // Iterator methods

    public function current(): ClassDependency
    {
        return parent::current();
    }

    public function addLine(LineInterface $statement)
    {
        $this->add($statement);
    }

    public function addLinesFromSource(SourceInterface $source)
    {
        foreach ($source->getSources() as $line) {
            $this->add($line);
        }
    }

    /**
     * @param SourceInterface[] $sources
     */
    public function addLinesFromSources(array $sources)
    {
        foreach ($sources as $source) {
            $this->addLinesFromSource($source);
        }
    }

    /**
     * @return LineInterface[]
     */
    public function getLines(): array
    {
        return $this->getAll();
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata();
    }

    /**
     * @return SourceInterface[]
     */
    public function getSources(): array
    {
        return $this->getAll();
    }
}
