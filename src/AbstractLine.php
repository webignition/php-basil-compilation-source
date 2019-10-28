<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

abstract class AbstractLine implements LineInterface
{
    protected $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
