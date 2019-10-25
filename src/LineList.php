<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class LineList implements SourceInterface
{
    const LAST_STATEMENT_INDEX = -1;

    /**
     * @var LineInterface[]
     */
    private $lines = [];

    public function __construct(array $lines = [])
    {
        $this->addLines($lines);
    }

    public function addLine(LineInterface $statement)
    {
        $this->lines[] = $statement;
    }

    public function addLines(array $lines)
    {
        foreach ($lines as $line) {
            if ($line instanceof LineInterface) {
                $this->addLine($line);
            }
        }
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        $lines = [];

        foreach ($this->lines as $line) {
            $lines[] = (string) $line;
        }

        return $lines;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->lines as $line) {
            if ($line instanceof StatementInterface) {
                $metadata->add($line->getMetadata());
            }
        }

        return $metadata;
    }

    /**
     * @return LineInterface[]
     */
    public function getLineObjects(): array
    {
        return $this->lines;
    }

    public function mutateLastStatement(callable $mutator)
    {
        $this->mutateStatement(self::LAST_STATEMENT_INDEX, function (StatementInterface $statement) use ($mutator) {
            return $statement->mutate($mutator);
        });
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies)
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof LineInterface) {
            $statement->getMetadata()->addClassDependencies($classDependencies);
        }
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof LineInterface) {
            $statement->getMetadata()->addVariableDependencies($variableDependencies);
        }
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof LineInterface) {
            $statement->getMetadata()->addVariableExports($variableExports);
        }
    }

    private function mutateStatement(int $index, callable $mutator)
    {
        $statementData = $this->getStatementData($index);
        if (is_array($statementData)) {
            list($statement, $statementIndex) = $statementData;

            $mutator($statement);
            $statementToLineIndex = $this->createStatementToLineIndex();
            $lineIndex = $statementToLineIndex[$statementIndex];

            $this->lines[$lineIndex] = $statement;
        }
    }

    private function getStatementData(int $index): ?array
    {
        $statements = array_filter($this->lines, function (LineInterface $line) {
            return $line instanceof StatementInterface;
        });

        $statements = array_values($statements);

        if ($index < 0) {
            $index = count($statements) + $index;
        }

        $statement = $statements[$index] ?? null;

        if (null === $statement) {
            return null;
        }

        return [
            $statement,
            $index,
        ];
    }

    private function createStatementToLineIndex(): array
    {
        $index = [];

        $statementIndex = 0;
        foreach ($this->lines as $lineIndex => $line) {
            if ($line instanceof StatementInterface) {
                $index[$statementIndex] = $lineIndex;
                $statementIndex++;
            }
        }

        return $index;
    }

    private function getStatement(int $index): ?StatementInterface
    {
        $statementData = $this->getStatementData($index);

        if (is_array($statementData)) {
            list($statement, $statementIndex) = $statementData;

            return $statement;
        }

        return null;
    }
}
