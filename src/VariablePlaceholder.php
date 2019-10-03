<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class VariablePlaceholder implements UniqueItemInterface
{
    const TEMPLATE = '{{ %s }}';

    private $name = '';
    private $id = '';

    public function __construct(string $name, string $id = '')
    {
        $id = '' === $id ? $name : $id;

        $this->name = $name;
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return sprintf(self::TEMPLATE, $this->name);
    }
}
