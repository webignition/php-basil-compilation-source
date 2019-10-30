<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface SourceInterface
{
    public function getMetadata(): MetadataInterface;

    /**
     * @return SourceInterface[]
     */
    public function getSources(): array;
}
