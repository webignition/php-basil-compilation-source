<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

use webignition\BasilCompilationSource\Metadata\MetadataInterface;

class Statement extends AbstractStatement implements StatementInterface
{
    public function __construct(string $content, ?MetadataInterface $metadata = null)
    {
        parent::__construct($content, LineTypes::STATEMENT, $metadata);
    }
}
