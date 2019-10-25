<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class Comment implements LineInterface
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isStatement(): bool
    {
        return false;
    }

    public function isComment(): bool
    {
        return true;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
