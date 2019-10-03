<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class CompilableSource implements CompilableSourceInterface
{
    /**
     * @var string[]
     */
    private $statements;

    private $compilationMetadata;

    public function __construct(array $statements, ?CompilationMetadataInterface $compilationMetadata = null)
    {
        $this->statements = $statements;
        $this->compilationMetadata = $compilationMetadata ?? new CompilationMetadata();
    }

    /**
     * @return string[]
     */
    public function getStatements(): array
    {
        return $this->statements;
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
