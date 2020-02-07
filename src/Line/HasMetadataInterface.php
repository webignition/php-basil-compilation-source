<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

use webignition\BasilCompilationSource\Metadata\MetadataInterface;

interface HasMetadataInterface
{
    public function getMetadata(): MetadataInterface;
}
