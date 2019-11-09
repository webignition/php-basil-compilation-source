<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

abstract class AbstractLine implements LineInterface
{
    protected $content;
    private $type;
    private $metadata;

    public function __construct(string $content, string $type, ?MetadataInterface $metadata = null)
    {
        $this->content = $content;
        $this->type = $type;
        $this->metadata = $metadata ?? new Metadata();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function getSources(): array
    {
        return [
            $this
        ];
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
