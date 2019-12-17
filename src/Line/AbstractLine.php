<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

abstract class AbstractLine implements LineInterface
{
    private $content;
    private $type;

    public function __construct(string $content, string $type)
    {
        $this->content = $content;
        $this->type = $type;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    protected function setContent(string $content): void
    {
        $this->content = $content;
    }
}
