<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Line\MethodInvocation\MethodInvocationInterface;
use webignition\BasilCompilationSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilationSource\Line\Statement;

class CodeBlockFactory
{
    private const INLINE_METHOD_INVOCATION_PATTERN = '/^.+->.+\(.+\)$/s';

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
                $lines[] = $line;
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

        if (0 === preg_match(self::INLINE_METHOD_INVOCATION_PATTERN, $lineString)) {
            return new Statement($lineString);
        }

        return $this->createObjectMethodInvocation($lineString);
    }

    private function createObjectMethodInvocation(string $lineString): MethodInvocationInterface
    {
        $firstBracketPosition = (int) strpos($lineString, '(');
        $objectAndMethodNamePart = substr($lineString, 0, $firstBracketPosition);
        $objectAndMethodName = explode('->', $objectAndMethodNamePart);
        list($object, $methodName) = $objectAndMethodName;

        $argumentsString = substr($lineString, $firstBracketPosition + 1, -1);
        $arguments = explode(', ', $argumentsString);
        if (substr_count($argumentsString, "\n") > 0) {
            $arguments = explode(',' . "\n", $argumentsString);
            $arguments = array_map('trim', $arguments);
        }

        return new ObjectMethodInvocation($object, $methodName, $arguments);
    }
}
