<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class StatementList implements SourceInterface
{
    const LAST_STATEMENT_INDEX = -1;

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
            $metadata->add($statement->getMetadata());
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

    public function mutateLastStatement(callable $mutator)
    {
        $this->mutateStatement(self::LAST_STATEMENT_INDEX, function (StatementInterface $statement) use ($mutator) {
            return $statement->mutate($mutator);
        });
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies)
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof StatementInterface) {
            $statement->getMetadata()->addClassDependencies($classDependencies);
        }
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof StatementInterface) {
            $statement->getMetadata()->addVariableDependencies($variableDependencies);
        }
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof StatementInterface) {
            $statement->getMetadata()->addVariableExports($variableExports);
        }
    }

    private function mutateStatement(int $index, callable $mutator)
    {
        $statement = $this->getStatement($index);

        if ($statement instanceof StatementInterface) {
            $mutator($statement);

            $this->statements[$this->translateIndex($index)] = $statement;
        }
    }

    private function getStatement(int $index): ?StatementInterface
    {
        return $this->statements[$this->translateIndex($index)] ?? null;
    }

    private function translateIndex(int $index): int
    {
        if ($index < 0) {
            $index = count($this->statements) + $index;
        }

        return $index;
    }
}
