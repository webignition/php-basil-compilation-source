<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class Source implements SourceInterface
{
    /**
     * @var SourceInterface[]
     */
    private $predecessors;

    /**
     * @var string[]
     */
    private $statements;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    public function __construct()
    {
        $this->predecessors = [];
        $this->statements = [];
        $this->metadata = new Metadata();
    }

    /**
     * @return string[]
     */
    public function getStatements(): array
    {
        $statements = [];

        foreach ($this->predecessors as $predecessor) {
            if ($predecessor instanceof SourceInterface) {
                $statements = array_merge($statements, $predecessor->getStatements());
            }
        }

        return array_merge($statements, $this->statements);
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();
        $metadata = $metadata->merge([$this->metadata]);

        foreach ($this->predecessors as $predecessor) {
            if ($predecessor instanceof SourceInterface) {
                $metadata = $metadata->merge([$predecessor->getMetadata()]);
            }
        }

        return $metadata;
    }

    /**
     * @return SourceInterface[]
     */
    public function getPredecessors(): array
    {
        return $this->predecessors;
    }

    /**
     * @param SourceInterface[] $predecessors
     *
     * @return SourceInterface
     */
    public function withPredecessors(array $predecessors): SourceInterface
    {
        $predecessors = array_filter($predecessors, function ($predecessor) {
            return $predecessor instanceof SourceInterface;
        });

        $new = clone $this;
        $new->predecessors = $predecessors;

        return $new;
    }

    /**
     * @param string[] $statements
     *
     * @return SourceInterface
     */
    public function withStatements(array $statements): SourceInterface
    {
        $new = clone $this;
        $new->statements = $statements;

        return $new;
    }

    public function withMetadata(MetadataInterface $metadata): SourceInterface
    {
        $new = clone $this;
        $new->metadata = $metadata;

        return $new;
    }

    public function prependStatement(int $index, string $content)
    {
        $this->mutateStatement($index, function (string $statement) use ($content) {
            return $content . $statement;
        });
    }

    public function appendStatement(int $index, string $content)
    {
        $this->mutateStatement($index, function (string $statement) use ($content) {
            return $statement . $content;
        });
    }

    public function __toString(): string
    {
        $statements = $this->getStatements();

        array_walk($statements, function (string &$statement) {
            $statement .= ';';
        });

        return implode("\n", $statements);
    }

    public function mutateStatement(int $index, callable $mutator)
    {
        $statements = $this->getStatements();

        if ($index < 0) {
            $index = count($statements) + $index;
        }

        $statementIndex = 0;

        foreach ($this->predecessors as $predecessor) {
            foreach ($predecessor->getStatements() as $predecessorStatementIndex => $predecessorStatement) {
                if ($statementIndex === $index) {
                    $predecessor->mutateStatement($predecessorStatementIndex, $mutator);

                    return;
                }

                $statementIndex++;
            }
        }

        if (array_key_exists($index, $statements)) {
            $this->statements[$index] = $mutator($statements[$index]);
        }
    }
}
