<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Line\Statement;
use webignition\BasilCompilationSource\Line\StatementInterface;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class CodeBlock extends AbstractBlock implements CodeBlockInterface
{
    private const LAST_STATEMENT_INDEX = -1;

    public function __construct(array $sources = [])
    {
        $lines = [];

        foreach ($sources as $source) {
            if ($source instanceof LineInterface) {
                $lines[] = $source;
            }

            if ($source instanceof BlockInterface) {
                $lines = array_merge($lines, $source->getLines());
            }
        }

        parent::__construct($lines);
    }


    protected function canLineBeAdded(LineInterface $line): bool
    {
        if ($line instanceof Comment) {
            return true;
        }

        if ($line instanceof EmptyLine) {
            return true;
        }

        if ($line instanceof Statement) {
            return true;
        }

        return false;
    }


    public function addLinesFromBlock(BlockInterface $block): void
    {
        foreach ($block->getLines() as $line) {
            $this->addLine($line);
        }
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

    public function mutateLastStatement(callable $mutator): void
    {
        $this->mutateStatement(self::LAST_STATEMENT_INDEX, function (StatementInterface $statement) use ($mutator) {
            $statement->mutate($mutator);
        });
    }

    public function addClassDependenciesToLastStatement(ClassDependencyCollection $classDependencies): void
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof StatementInterface) {
            $statement->addClassDependencies($classDependencies);
        }
    }

    public function addVariableDependenciesToLastStatement(VariablePlaceholderCollection $variableDependencies): void
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof StatementInterface) {
            $statement->addVariableDependencies($variableDependencies);
        }
    }

    public function addVariableExportsToLastStatement(VariablePlaceholderCollection $variableExports): void
    {
        $statement = $this->getStatement(self::LAST_STATEMENT_INDEX);

        if ($statement instanceof StatementInterface) {
            $statement->addVariableExports($variableExports);
        }
    }

    /**
     * @param string[] $content
     *
     * @return CodeBlock
     */
    public static function fromContent(array $content): CodeBlock
    {
        $lines = [];

        foreach ($content as $string) {
            $line = self::createLineObjectFromLineString($string);

            if ($line instanceof LineInterface) {
                $lines[] = self::createLineObjectFromLineString($string);
            }
        }

        return new CodeBlock($lines);
    }

    private static function createLineObjectFromLineString(string $lineString): ?LineInterface
    {
        if ('' === trim($lineString)) {
            return new EmptyLine();
        }

        $lineLength = strlen($lineString);

        if ($lineLength > 2 && '//' === substr($lineString, 0, 2)) {
            return new Comment(ltrim($lineString, '/ '));
        }

        $useStatementPrefix = 'use ';
        $useStatementPrefixLength = strlen($useStatementPrefix);

        if (
            $lineLength >= $useStatementPrefixLength &&
            $useStatementPrefix === substr($lineString, 0, $useStatementPrefixLength)
        ) {
            return null;
        }

        return new Statement($lineString);
    }

    private function mutateStatement(int $index, callable $mutator): void
    {
        $indexedStatement = $this->findIndexedStatement($index);

        if ($indexedStatement instanceof IndexedStatement) {
            $statement = $indexedStatement->getStatement();
            $statementIndex = $indexedStatement->getIndex();

            $mutator($statement);
            $statementToLineIndex = $this->createStatementToLineIndex();
            $lineIndex = $statementToLineIndex[$statementIndex];

            $this->lines[$lineIndex] = $statement;
        }
    }

    private function findIndexedStatement(int $index): ?IndexedStatement
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

        return new IndexedStatement($statement, $index);
    }

    /**
     * @return array<int, int>
     */
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
        $indexedStatement = $this->findIndexedStatement($index);

        return $indexedStatement instanceof IndexedStatement
            ? $indexedStatement->getStatement()
            : null;
    }
}
