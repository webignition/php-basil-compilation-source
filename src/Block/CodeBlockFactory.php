<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Line\Statement;

class CodeBlockFactory
{
    public static function createFactory(): CodeBlockFactory
    {
        return new CodeBlockFactory();
    }

    /**
     * @param string[] $content
     *
     * @return CodeBlock
     */
    public function createFromContent(array $content): CodeBlock
    {
        $lines = [];

        foreach ($content as $string) {
            $line = $this->createLineObjectFromLineString($string);

            if ($line instanceof LineInterface) {
                $lines[] = $this->createLineObjectFromLineString($string);
            }
        }

        return new CodeBlock($lines);
    }

    private function createLineObjectFromLineString(string $lineString): ?LineInterface
    {
        if ('' === trim($lineString)) {
            return new EmptyLine();
        }

        $lineLength = strlen($lineString);

        if ($lineLength > 2 && '//' === substr($lineString, 0, 2)) {
            return new Comment(ltrim($lineString, '/ '));
        }

        $useStatementPrefix = 'use ';
        $useStatementPrefixLength = strlen($useStatementPrefix);

        if (
            $lineLength >= $useStatementPrefixLength &&
            $useStatementPrefix === substr($lineString, 0, $useStatementPrefixLength)
        ) {
            return null;
        }

        return new Statement($lineString);
    }
}
