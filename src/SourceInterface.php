<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

use webignition\BasilCompilationSource\Metadata\MetadataInterface;

interface SourceInterface
{
    public function getMetadata(): MetadataInterface;

    /**
     * @return SourceInterface[]
     */
    public function getSources(): array;
}
