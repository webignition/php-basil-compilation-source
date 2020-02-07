<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Block;

use webignition\BasilCompilationSource\Block\CodeBlockFactory;
use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\Statement;
use webignition\BasilCompilationSource\Block\CodeBlock;

class CodeBlockFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createFromContentDataProvider
     *
     * @param string[] $content
     * @param CodeBlock $expectedBlock
     */
    public function testCreateFromContent(array $content, CodeBlock $expectedBlock)
    {
        $factory = CodeBlockFactory::createFactory();

        $this->assertEquals($expectedBlock, $factory->createFromContent($content));
    }

    public function createFromContentDataProvider(): array
    {
        return [
            'empty' => [
                'content' => [],
                'expectedBlock' => new CodeBlock(),
            ],
            'non-empty' => [
                'content' => [
                    '//comment without leading whitespace',
                    '// comment with single leading whitespace',
                    '//       comment with multiple leading whitespace',
                    '',
                    '$x = $y',
                    'use Foo',
                    '$object->methodName($arg, $arg2)',
                ],
                'expectedBlock' => new CodeBlock([
                    new Comment('comment without leading whitespace'),
                    new Comment('comment with single leading whitespace'),
                    new Comment('comment with multiple leading whitespace'),
                    new EmptyLine(),
                    new Statement('$x = $y'),
                    new Statement('$object->methodName($arg, $arg2)'),
                ]),
            ],
        ];
    }
}
