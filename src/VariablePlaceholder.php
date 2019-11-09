<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class VariablePlaceholder
{
    public const TEMPLATE = '{{ %s }}';

    private $name = '';

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return sprintf(self::TEMPLATE, $this->name);
    }
}
