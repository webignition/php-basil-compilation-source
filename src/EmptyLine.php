<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class EmptyLine extends AbstractLine implements LineInterface
{
    const TYPE = 'empty';

    public function __construct()
    {
        parent::__construct('', self::TYPE);
    }
}
