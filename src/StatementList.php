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

        foreach ($this->statements as $statementIndex => $statement) {
            if ($statementIndex === $index) {
                $this->statements[$statementIndex] = $mutator($statement);
            }
        }
    }
}
