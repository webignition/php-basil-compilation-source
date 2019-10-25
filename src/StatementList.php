<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class StatementList implements StatementListInterface
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

    private function mutateStatement(int $index, callable $mutator)
    {
        $statement = $this->getStatement($index);

        if ($statement instanceof StatementInterface) {
            $this->replaceStatement($index, $mutator($statement));
        }
    }

    public function mutateLastStatement(callable $mutator)
    {
        $this->mutateStatement(self::LAST_STATEMENT_INDEX, function (StatementInterface $statement) use ($mutator) {
            return $statement->mutate($mutator);
        });
    }

    private function replaceStatement(int $index, StatementInterface $statement)
    {
        $currentStatement = $this->getStatement($index);

        if ($currentStatement instanceof StatementInterface) {
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

    private function addClassDependencies(int $index, ClassDependencyCollection $classDependencies)
    {
        $statement = $this->getStatement($index);

        if ($statement instanceof StatementInterface) {
            $statement->getMetadata()->addClassDependencies($classDependencies);
        }
    }

    public function addVariableDependencies(int $index, VariablePlaceholderCollection $variableDependencies)
    {
        $statement = $this->getStatement($index);

        if ($statement instanceof StatementInterface) {
            $statement->getMetadata()->addVariableDependencies($variableDependencies);
        }
    }

    public function addVariableExports(int $index, VariablePlaceholderCollection $variableExports)
    {
        $statement = $this->getStatement($index);

        if ($statement instanceof StatementInterface) {
            $statement->getMetadata()->addVariableExports($variableExports);
        }
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies)
    {
        $this->addClassDependencies(self::LAST_STATEMENT_INDEX, $classDependencies);
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        $this->addVariableDependencies(self::LAST_STATEMENT_INDEX, $variableDependencies);
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $this->addVariableExports(self::LAST_STATEMENT_INDEX, $variableExports);
    }
}
