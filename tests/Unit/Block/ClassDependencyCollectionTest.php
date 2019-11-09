<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Block;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\Statement;
use webignition\BasilCompilationSource\Metadata\Metadata;

class ClassDependencyCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $acceptedLines = [
            new ClassDependency(ClassDependency::class),
        ];

        $block = new ClassDependencyCollection(array_merge($acceptedLines, [
            new Statement('statement1'),
            new Statement('statement2'),
            new EmptyLine(),
            new Comment('comment'),
        ]));

        $this->assertSame($acceptedLines, $block->getLines());
    }

    public function testAddLine()
    {
        $collection = new ClassDependencyCollection();
        $this->assertEquals([], $collection->getLines());

        $emptyLine = new EmptyLine();
        $collection->addLine($emptyLine);
        $this->assertEquals([], $collection->getLines());

        $comment = new Comment('comment');
        $collection->addLine($comment);
        $this->assertEquals([], $collection->getLines());

        $statement = new Statement('$x = $y');
        $collection->addLine($statement);
        $this->assertEquals([], $collection->getLines());

        $classDependency = new ClassDependency(ClassDependency::class);
        $collection->addLine($classDependency);
        $collection->addLine($classDependency);
        $collection->addLine($classDependency);
        $this->assertEquals(
            [
                $classDependency,
            ],
            $collection->getLines()
        );
    }

    /**
     * @dataProvider addLinesFromSourcesDataProvider
     */
    public function testAddLinesFromSources(ClassDependencyCollection $collection, array $lines, array $expectedLines)
    {
        $collection->addLinesFromSources($lines);

        $this->assertEquals($expectedLines, $collection->getLines());
    }

    public function addLinesFromSourcesDataProvider(): array
    {
        return [
            'empty collection, empty lines' => [
                'collection' => new ClassDependencyCollection(),
                'lines' => [],
                'expectedLines' => [],
            ],
            'empty collection, non-empty lines' => [
                'collection' => new ClassDependencyCollection(),
                'lines' => [
                    new ClassDependency(ClassDependency::class),
                    new ClassDependency(ClassDependencyCollection::class),
                ],
                'expectedLines' => [
                    new ClassDependency(ClassDependency::class),
                    new ClassDependency(ClassDependencyCollection::class),
                ],
            ],
            'non-empty collection, non-empty lines' => [
                'collection' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                ]),
                'lines' => [
                    new Statement('statement2'),
                    new EmptyLine(),
                    new Comment('comment2'),
                    new ClassDependency(ClassDependencyCollection::class),
                ],
                'expectedLines' => [
                    new ClassDependency(ClassDependency::class),
                    new ClassDependency(ClassDependencyCollection::class),
                ],
            ],
        ];
    }

    /**
     * @dataProvider getLinesDataProvider
     */
    public function testGetLines(ClassDependencyCollection $collection, array $expectedLines)
    {
        $this->assertEquals($expectedLines, $collection->getLines());
    }

    /**
     * @dataProvider getLinesDataProvider
     */
    public function testGetContents(ClassDependencyCollection $collection, array $expectedLineObjects)
    {
        $this->assertEquals($expectedLineObjects, $collection->getSources());
    }

    public function getLinesDataProvider(): array
    {
        return [
            'empty' => [
                'collection' => new ClassDependencyCollection([]),
                'expectedLines' => [],
            ],
            'non-empty' => [
                'collection' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                    new ClassDependency(ClassDependencyCollection::class),
                ]),
                'expectedLines' => [
                    new ClassDependency(ClassDependency::class),
                    new ClassDependency(ClassDependencyCollection::class),
                ],
            ],
        ];
    }

    public function testGetMetadata()
    {
        $collection = new ClassDependencyCollection();
        $this->assertEquals(new Metadata(), $collection->getMetadata());

        $collection->addLine(new ClassDependency(ClassDependency::class));
        $collection->addLine(new ClassDependency(ClassDependencyCollection::class));

        $this->assertEquals(new Metadata(), $collection->getMetadata());
    }
}
