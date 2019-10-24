<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class StatementList implements StatementListInterface
{
    /**
     * @var StatementInterface[]
     */
    private $statements = [];

    public function __construct(array $statements)
    {
        foreach ($statements as $statement) {
            if ($statement instanceof StatementInterface) {
                $this->statements[] = $statement;
            }
        }
    }

    /**
     * @return string[]
     */
    public function getStatements(): array
    {
        $statements = [];

        foreach ($this->statements as $statement) {
            $statements[] = (string) $statement;
        }

        return $statements;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->statements as $statement) {
            $metadata = $metadata->merge([$statement->getMetadata()]);
        }

        return $metadata;
    }

    /**
     * @return StatementInterface[]
     */
    public function getStatementObjects(): array
    {
        return $this->statements;
    }

    public function prependStatement(int $index, string $content)
    {
        $this->mutateStatement($index, function (StatementInterface $statement) use ($content) {
            return $statement->prepend($content);
        });
    }

    public function appendStatement(int $index, string $content)
    {
        $this->mutateStatement($index, function (StatementInterface $statement) use ($content) {
            return $statement->append($content);
        });
    }

    public function mutateStatement(int $index, callable $mutator)
    {
        if ($index < 0) {
            $index = count($this->statements) + $index;
        }

        if (array_key_exists($index, $this->statements)) {
            $this->replaceStatement($index, $mutator($this->statements[$index]));
        }
    }

    public function replaceStatement(int $index, StatementInterface $statement)
    {
        if ($index < 0) {
            $index = count($this->statements) + $index;
        }

        if (array_key_exists($index, $this->statements)) {
            $this->statements[$index] = $statement;
        }
    }

    public function getStatement(int $index): ?StatementInterface
    {
        if ($index < 0) {
            $index = count($this->statements) + $index;
        }

        return $this->statements[$index] ?? null;
    }
}
