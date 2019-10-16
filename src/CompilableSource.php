<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

use webignition\BasilCompilationSource\CompilationMetadataInterface as Metadata;

class CompilableSource implements CompilableSourceInterface
{
    /**
     * @var CompilableSourceInterface[]
     */
    private $predecessors;

    /**
     * @var string[]
     */
    private $statements;

    /**
     * @var CompilationMetadataInterface
     */
    private $compilationMetadata;

    public function __construct()
    {
        $this->predecessors = [];
        $this->statements = [];
        $this->compilationMetadata = new CompilationMetadata();
    }

    /**
     * @return string[]
     */
    public function getStatements(): array
    {
        $statements = [];

        foreach ($this->predecessors as $predecessor) {
            if ($predecessor instanceof CompilableSourceInterface) {
                $statements = array_merge($statements, $predecessor->getStatements());
            }
        }

        return array_merge($statements, $this->statements);
    }

    public function getCompilationMetadata(): Metadata
    {
        $compilationMetadata = new CompilationMetadata();
        $compilationMetadata = $compilationMetadata->merge([$this->compilationMetadata]);

        foreach ($this->predecessors as $predecessor) {
            if ($predecessor instanceof CompilableSourceInterface) {
                $compilationMetadata = $compilationMetadata->merge([$predecessor->getCompilationMetadata()]);
            }
        }

        return $compilationMetadata;
    }

    /**
     * @return CompilableSourceInterface[]
     */
    public function getPredecessors(): array
    {
        return $this->predecessors;
    }

    /**
     * @param CompilableSourceInterface[] $predecessors
     *
     * @return CompilableSourceInterface
     */
    public function withPredecessors(array $predecessors): CompilableSourceInterface
    {
        $predecessors = array_filter($predecessors, function ($predecessor) {
            return $predecessor instanceof CompilableSourceInterface;
        });

        $new = clone $this;
        $new->predecessors = $predecessors;

        return $new;
    }

    /**
     * @param string[] $statements
     *
     * @return CompilableSourceInterface
     */
    public function withStatements(array $statements): CompilableSourceInterface
    {
        $new = clone $this;
        $new->statements = $statements;

        return $new;
    }

    public function withCompilationMetadata(Metadata $compilationMetadata): CompilableSourceInterface
    {
        $new = clone $this;
        $new->compilationMetadata = $compilationMetadata;

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
        return implode("\n", $this->statements);
    }

    private function mutateStatement(int $index, callable $mutator)
    {
        if ($index < 0) {
            $index = count($this->statements) + $index;
        }

        $statement = $this->statements[$index] ?? null;

        if (null !== $statement) {
            $this->statements[$index] = $mutator($statement);
        }
    }
}
