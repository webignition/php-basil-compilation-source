<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

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

    public function __construct(
        array $predecessors = [],
        array $statements = [],
        ?CompilationMetadataInterface $compilationMetadata = null
    ) {
        $this->statements = $statements;
        $this->compilationMetadata = $compilationMetadata ?? new CompilationMetadata();

        $this->predecessors = [];

        foreach ($predecessors as $predecessor) {
            if ($predecessor instanceof CompilableSourceInterface) {
                $this->addPredecessor($predecessor);
            }
        }
    }

    public function addPredecessor(CompilableSourceInterface $predecessor)
    {
        $this->predecessors[] = $predecessor;

        $this->compilationMetadata = $this->compilationMetadata->merge([
            $predecessor->getCompilationMetadata()
        ]);
    }

    /**
     * @return string[]
     */
    public function getStatements(): array
    {
        $statements = [];

        foreach ($this->predecessors as $predecessor) {
            $statements = array_merge($statements, $predecessor->getStatements());
        }

        return array_merge($statements, $this->statements);
    }

    public function getCompilationMetadata(): CompilationMetadataInterface
    {
        return $this->compilationMetadata;
    }

    public function withCompilationMetadata(
        CompilationMetadataInterface $compilationMetadata
    ): CompilableSourceInterface {
        $new = clone $this;
        $new->compilationMetadata = $compilationMetadata;

        return $new;
    }

    public function mergeCompilationData(array $compilationDataCollection): CompilableSourceInterface
    {
        $new = clone $this;
        $new->compilationMetadata = $new->compilationMetadata->merge($compilationDataCollection);

        return $new;
    }

    public function __toString(): string
    {
        return implode("\n", $this->statements);
    }
}
