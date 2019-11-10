<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Block;

use webignition\BasilCompilationSource\Block\DocBlock;
use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\Statement;
use webignition\BasilCompilationSource\Metadata\Metadata;

class DocBlockTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $acceptedLines = [
            new EmptyLine(),
            new Comment('comment'),
        ];

        $docBlock = new DocBlock(array_merge($acceptedLines, [
            new ClassDependency(ClassDependency::class),
            new Statement('statement1'),
            new Statement('statement2'),
        ]));

        $this->assertSame($acceptedLines, $docBlock->getLines());
    }

    public function testAddLine()
    {
        $docBlock = new DocBlock();
        $this->assertEquals([], $docBlock->getLines());

        $emptyLine = new EmptyLine();
        $docBlock->addLine($emptyLine);
        $this->assertEquals(
            [
                $emptyLine,
            ],
            $docBlock->getLines()
        );

        $comment = new Comment('comment');
        $docBlock->addLine($comment);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
            ],
            $docBlock->getLines()
        );

        $statement = new Statement('$x = $y');
        $docBlock->addLine($statement);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
            ],
            $docBlock->getLines()
        );

        $classDependency = new ClassDependency(ClassDependency::class);
        $docBlock->addLine($classDependency);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
            ],
            $docBlock->getLines()
        );
    }

    /**
     * @dataProvider addLinesFromSourcesDataProvider
     */
    public function testAddLinesFromSources(DocBlock $docBlock, array $lines, array $expectedLines)
    {
        $docBlock->addLinesFromSources($lines);

        $this->assertEquals($expectedLines, $docBlock->getLines());
    }

    public function addLinesFromSourcesDataProvider(): array
    {
        return [
            'empty block, empty lines' => [
                'block' => new DocBlock(),
                'lines' => [],
                'expectedLines' => [],
            ],
            'empty block, non-empty lines' => [
                'block' => new DocBlock(),
                'lines' => [
                    new Statement('statement'),
                    new EmptyLine(),
                    new Comment('comment'),
                ],
                'expectedLines' => [
                    new EmptyLine(),
                    new Comment('comment'),
                ],
            ],
            'non-empty block, non-empty lines' => [
                'block' => new DocBlock([
                    new Statement('statement1'),
                    new EmptyLine(),
                    new Comment('comment1'),
                ]),
                'lines' => [
                    new Statement('statement2'),
                    new EmptyLine(),
                    new Comment('comment2'),
                    new ClassDependency(ClassDependency::class),
                ],
                'expectedLines' => [
                    new EmptyLine(),
                    new Comment('comment1'),
                    new EmptyLine(),
                    new Comment('comment2'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider getLinesDataProvider
     */
    public function testGetLines(DocBlock $docBlock, array $expectedLines)
    {
        $this->assertEquals($expectedLines, $docBlock->getLines());
    }

    /**
     * @dataProvider getLinesDataProvider
     */
    public function testGetContents(DocBlock $docBlock, array $expectedLineObjects)
    {
        $this->assertEquals($expectedLineObjects, $docBlock->getSources());
    }

    public function getLinesDataProvider(): array
    {
        return [
            'empty' => [
                'block' => new DocBlock([]),
                'expectedLines' => [],
            ],
            'non-empty' => [
                'block' => new DocBlock([
                    new Statement('statement1'),
                    new Statement('statement2'),
                    new EmptyLine(),
                    new Comment('comment'),
                ]),
                'expectedLines' => [
                    new EmptyLine(),
                    new Comment('comment'),
                ],
            ],
        ];
    }

    public function testGetMetadata()
    {
        $docBlock = new DocBlock();
        $this->assertEquals(new Metadata(), $docBlock->getMetadata());

        $docBlock->addLine(new Comment('comment'));
        $this->assertEquals(new Metadata(), $docBlock->getMetadata());
    }
}
