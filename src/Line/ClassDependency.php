<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

class ClassDependency extends AbstractLine
{
    public function __construct(string $className, ?string $alias = null)
    {
        $content = $className;

        if (null !== $alias) {
            $content .= ' as ' . $alias;
        }

        parent::__construct($content, LineTypes::USE_STATEMENT);
    }
}
