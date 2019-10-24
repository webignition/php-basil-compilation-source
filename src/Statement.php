<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class Statement implements StatementInterface
{
    private $content;
    private $metadata;

    public function __construct(string $content, ?MetadataInterface $metadata = null)
    {
        $this->content = $content;
        $this->metadata = $metadata ?? new Metadata();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string[]
     */
    public function getStatements(): array
    {
        return [$this->content];
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
