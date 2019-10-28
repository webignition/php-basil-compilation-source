<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

abstract class AbstractLine implements LineInterface
{
    protected $content;
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
}
