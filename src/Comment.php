<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class Comment extends AbstractLine
{
    const TYPE = 'comment';

    public function __construct(string $content)
    {
        parent::__construct($content, self::TYPE);
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
}
