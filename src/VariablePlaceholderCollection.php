<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class VariablePlaceholderCollection implements \Iterator, \Countable
{
    /**
     * @var VariablePlaceholder[]
     */
    private $variablePlaceholders = [];

    private $iteratorIndex = [];
    private $iteratorPosition = 0;

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public static function createCollection(array $names): VariablePlaceholderCollection
    {
        $collection = new VariablePlaceholderCollection();

        foreach ($names as $name) {
            if (is_string($name)) {
                $collection->create($name);
            }
        }

        return $collection;
    }

    public function create(string $name): VariablePlaceholder
    {
        $variablePlaceholder = $this->find($name);

        if (null === $variablePlaceholder) {
            $variablePlaceholder = new VariablePlaceholder($name);
            $this->add($variablePlaceholder);
        }

        return $variablePlaceholder;
    }

    public function merge(array $collections)
    {
        foreach ($collections as $collection) {
            if ($collection instanceof VariablePlaceholderCollection) {
                $localCollection = clone $collection;

                foreach ($localCollection as $variablePlaceholder) {
                    $this->add($variablePlaceholder);
                }
            }
        }
    }

    private function add(VariablePlaceholder $variablePlaceholder)
    {
        $name = $variablePlaceholder->getName();

        if (!array_key_exists($name, $this->variablePlaceholders)) {
            $indexPosition = count($this->variablePlaceholders);

            $this->variablePlaceholders[$name] = $variablePlaceholder;
            $this->iteratorIndex[$indexPosition] = $name;
        }
    }

    private function find(string $name): ?VariablePlaceholder
    {
        return $this->variablePlaceholders[$name] ?? null;
    }

    // Iterator methods

    public function rewind()
    {
        $this->iteratorPosition = 0;
    }

    public function current(): VariablePlaceholder
    {
        $key = $this->iteratorIndex[$this->iteratorPosition];

        return $this->variablePlaceholders[$key];
    }

    public function key(): string
    {
        return $this->iteratorIndex[$this->iteratorPosition];
    }

    public function next()
    {
        ++$this->iteratorPosition;
    }

    public function valid(): bool
    {
        $key = $this->iteratorIndex[$this->iteratorPosition] ?? null;

        return $key !== null;
    }

    // Countable methods

    public function count(): int
    {
        return count($this->variablePlaceholders);
    }
}
