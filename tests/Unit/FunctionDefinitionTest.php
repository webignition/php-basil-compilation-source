<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\Comment;
use webignition\BasilCompilationSource\EmptyLine;
use webignition\BasilCompilationSource\FunctionDefinition;
use webignition\BasilCompilationSource\LineListInterface;
use webignition\BasilCompilationSource\SourceInterface;
use webignition\BasilCompilationSource\Statement;
use webignition\BasilCompilationSource\LineList;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class FunctionDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name, LineListInterface $content, $arguments, array $expectedArguments)
    {
        $functionDefinition = new FunctionDefinition($name, $content, $arguments);

        $this->assertSame($name, $functionDefinition->getName());
        $this->assertSame($content->getSources(), $functionDefinition->getSources());
        $this->assertEquals($expectedArguments, $functionDefinition->getArguments());
        $this->assertEquals($content->getMetadata(), $functionDefinition->getMetadata());
    }

    public function constructDataProvider(): array
    {
        return [
            'without arguments' => [
                'name' => 'withoutArguments',
                'content' => new LineList([new Statement('statement')]),
                'arguments' => null,
                'expectedArguments' => [],
            ],
            'with arguments' => [
                'name' => 'withArguments',
                'content' => new LineList([new Statement('statement')]),
                'arguments' => ['a', 'b', 'c'],
                'expectedArguments' => ['a', 'b', 'c'],
            ],
        ];
    }

    /**
     * @dataProvider addLinesFromSourceDataProvider
     */
    public function testAddLinesFromSource(
        FunctionDefinition $functionDefinition,
        SourceInterface $source,
        array $expectedLines
    ) {
        $functionDefinition->addLinesFromSource($source);

        $this->assertEquals($expectedLines, $functionDefinition->getLines());
    }

    public function addLinesFromSourceDataProvider(): array
    {
        return [
            'empty list, non-empty source' => [
                'functionDefinition' => new FunctionDefinition('name', new LineList()),
                'source' => new Statement('statement'),
                'expectedLines' => [
                    new Statement('statement'),
                ],
            ],
            'non-empty list, non-empty lines' => [
                'functionDefinition' => new FunctionDefinition(
                    'name',
                    new LineList([
                        new Statement('statement1'),
                        new EmptyLine(),
                        new Comment('comment1'),
                    ])
                ),
                'source' => new Statement('statement2'),
                'expectedLines' => [
                    new Statement('statement1'),
                    new EmptyLine(),
                    new Comment('comment1'),
                    new Statement('statement2'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider addLinesFromSourcesDataProvider
     */
    public function testAddLinesFromSources(
        FunctionDefinition $functionDefinition,
        array $sources,
        array $expectedLines
    ) {
        $functionDefinition->addLinesFromSources($sources);

        $this->assertEquals($expectedLines, $functionDefinition->getLines());
    }

    public function addLinesFromSourcesDataProvider(): array
    {
        return [
            'empty list, empty lines' => [
                'functionDefinition' => new FunctionDefinition('name', new LineList()),
                'sources' => [],
                'expectedLines' => [],
            ],
            'empty list, non-empty lines' => [
                'functionDefinition' => new FunctionDefinition('name', new LineList()),
                'lines' => [
                    new Statement('statement'),
                    new EmptyLine(),
                    new Comment('comment'),
                ],
                'expectedLines' => [
                    new Statement('statement'),
                    new EmptyLine(),
                    new Comment('comment'),
                ],
            ],
            'non-empty list, non-empty lines' => [
                'functionDefinition' => new FunctionDefinition(
                    'name',
                    new LineList([
                        new Statement('statement1'),
                        new EmptyLine(),
                        new Comment('comment1'),
                    ])
                ),
                'lines' => [
                    new Statement('statement2'),
                    new EmptyLine(),
                    new Comment('comment2'),
                ],
                'expectedLines' => [
                    new Statement('statement1'),
                    new EmptyLine(),
                    new Comment('comment1'),
                    new Statement('statement2'),
                    new EmptyLine(),
                    new Comment('comment2'),
                ],
            ],
        ];
    }

    public function testAddLine()
    {
        $functionDefinition = new FunctionDefinition(
            'name',
            new LineList([
                new Statement('statement1'),
                new EmptyLine(),
                new Comment('comment1'),
            ])
        );

        $this->assertEquals(
            [
                new Statement('statement1'),
                new EmptyLine(),
                new Comment('comment1'),
            ],
            $functionDefinition->getLines()
        );

        $functionDefinition->addLine(new EmptyLine());

        $this->assertEquals(
            [
                new Statement('statement1'),
                new EmptyLine(),
                new Comment('comment1'),
                new EmptyLine(),
            ],
            $functionDefinition->getLines()
        );
    }

    public function testGetStatementObjects()
    {
        $statement = new Statement('statement');
        $functionDefinition = new FunctionDefinition('name', new LineList([$statement]));

        $this->assertEquals([$statement], $functionDefinition->getLines());
    }

    public function testMutateLastStatement()
    {
        $statement = new Statement('content');
        $functionDefinition = new FunctionDefinition('name', new LineList([$statement]));

        $functionDefinition->mutateLastStatement(function (string $content) {
            return '!' . $content . '!';
        });

        $this->assertEquals('!content!', $statement->getContent());
    }

    public function testAddClassDependenciesToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(new ClassDependencyCollection([]), $statement->getMetadata()->getClassDependencies());

        $functionDefinition = new FunctionDefinition('name', new LineList([$statement]));

        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $functionDefinition->addClassDependenciesToLastStatement($classDependencies);
        $this->assertEquals($classDependencies, $statement->getMetadata()->getClassDependencies());
    }

    public function testAddVariableDependenciesToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableDependencies()
        );

        $functionDefinition = new FunctionDefinition('name', new LineList([$statement]));
        $variableDependencies = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $functionDefinition->addVariableDependenciesToLastStatement($variableDependencies);
        $this->assertEquals($variableDependencies, $statement->getMetadata()->getVariableDependencies());
    }

    public function testAddVariableExportsToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableExports()
        );

        $functionDefinition = new FunctionDefinition('name', new LineList([$statement]));
        $variableExports = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $functionDefinition->addVariableExportsToLastStatement($variableExports);
        $this->assertEquals($variableExports, $statement->getMetadata()->getVariableExports());
    }

    public function testJsonSerialize()
    {
        $functionDefinition = new FunctionDefinition('functionName', new LineList([
            new EmptyLine(),
        ]));

        $this->assertSame(
            [
                'type' => 'function',
                'name' => 'functionName',
                'line-list' => [
                    'type' => 'line-list',
                    'lines' => [
                        [
                            'type' => 'empty',
                            'content' => '',
                        ],
                    ],
                ],
            ],
            $functionDefinition->jsonSerialize()
        );
    }
}
