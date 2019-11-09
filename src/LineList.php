<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;

class LineList implements LineListInterface, MutableListLineListInterface
{
    private const LAST_STATEMENT_INDEX = -1;

    /**
     * @var LineInterface[]
     */
    private $lines = [];

    public function __construct(array $sources = [])
    {
        $this->addLinesFromSources($sources);
    }

    public function addLine(LineInterface $statement)
    {
        $this->lines[] = $statement;
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
            $statement->addClassDependencies($classDependencies);
        }
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies)
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof StatementInterface) {
            $statement->addVariableDependencies($variableDependencies);
        }
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports)
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof StatementInterface) {
            $statement->addVariableExports($variableExports);
        }
    }

    public function getSources(): array
    {
        return $this->getLines();
    }

    public static function fromContent(array $content): LineList
    {
        $lines = [];

        foreach ($content as $string) {
            $lines[] = self::createLineObjectFromLineString($string);
        }

        return new LineList($lines);
    }

    private static function createLineObjectFromLineString(string $lineString): LineInterface
    {
        if ('' === trim($lineString)) {
            return new EmptyLine();
        }

        if (strlen($lineString) > 2 && '//' === substr($lineString, 0, 2)) {
            return new Comment(ltrim($lineString, '/ '));
        }

        return new Statement($lineString);
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
