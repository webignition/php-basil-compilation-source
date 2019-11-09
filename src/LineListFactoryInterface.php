<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface LineListFactoryInterface
{
    /**
     * @param string[] $content
     *
     * @return LineListInterface
     */
    public static function fromContent(array $content): LineListInterface;
}
