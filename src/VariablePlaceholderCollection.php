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

    /**
     * @param VariablePlaceholder[] $items
     */
    public function __construct(array $items = [])
    {
        $this->iteratorIndex = [];
        $this->iteratorPosition = 0;

        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param string[] $names
     *
     * @return VariablePlaceholderCollection
     */
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

    /**
     * @param VariablePlaceholderCollection[] $collections
     */
    public function merge(array $collections): void
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

    public function add(VariablePlaceholder $variablePlaceholder): void
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

    public function rewind(): void
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

    public function next(): void
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
