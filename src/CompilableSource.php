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

    public function mergeCompilationData(array $compilationDataCollection): CompilableSourceInterface
    {
        $new = clone $this;
        $new->compilationMetadata = $new->compilationMetadata->merge($compilationDataCollection);

        return $new;
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

    public function __toString(): string
    {
        return implode("\n", $this->statements);
    }

}
