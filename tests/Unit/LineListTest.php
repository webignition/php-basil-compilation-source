<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\Comment;
use webignition\BasilCompilationSource\EmptyLine;
use webignition\BasilCompilationSource\MetadataInterface;
use webignition\BasilCompilationSource\Statement;
use webignition\BasilCompilationSource\LineList;
use webignition\BasilCompilationSource\Metadata;
use webignition\BasilCompilationSource\StatementInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class LineListTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $lines = [
            new Statement('statement1'),
            new Statement('statement2'),
            new EmptyLine(),
            new Comment('comment'),
        ];

        $lineList = new LineList($lines);

        $this->assertSame($lines, $lineList->getLines());
    }

    /**
     * @dataProvider addLinesFromSourcesDataProvider
     */
    public function testAddLinesFromSources(LineList $lineList, array $statements, array $expectedLines)
    {
        $lineList->addLinesFromSources($statements);

        $this->assertEquals($expectedLines, $lineList->getLines());
    }

    public function addLinesFromSourcesDataProvider(): array
    {
        return [
            'empty list, empty lines' => [
                'lineList' => new LineList(),
                'lines' => [],
                'expectedLines' => [],
            ],
            'empty list, non-empty lines' => [
                'lineList' => new LineList(),
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
                'lineList' => new LineList([
                    new Statement('statement1'),
                    new EmptyLine(),
                    new Comment('comment1'),
                ]),
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

    /**
     * @dataProvider getLinesDataProvider
     */
    public function testGetLines(LineList $lineList, array $expectedLines)
    {
        $this->assertEquals($expectedLines, $lineList->getLines());
    }

    /**
     * @dataProvider getLinesDataProvider
     */
    public function testGetContents(LineList $lineList, array $expectedLineObjects)
    {
        $this->assertEquals($expectedLineObjects, $lineList->getSources());
    }

    public function getLinesDataProvider(): array
    {
        return [
            'empty' => [
                'lineList' => new LineList([]),
                'expectedLines' => [],
            ],
            'non-empty' => [
                'lineList' => new LineList([
                    new Statement('statement1'),
                    new Statement('statement2'),
                    new EmptyLine(),
                    new Comment('comment'),
                ]),
                'expectedLines' => [
                    new Statement('statement1'),
                    new Statement('statement2'),
                    new EmptyLine(),
                    new Comment('comment'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(LineList $lineList, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $lineList->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'lineList' => new LineList([]),
                'expectedMetadata' => new Metadata(),
            ],
            'non-statement lines' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new EmptyLine(),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'no metadata' => [
                'lineList' => new LineList([
                    new Statement('statement1'),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'lineList' => new LineList([
                    new Statement('statement1', (new Metadata())
                        ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                            'DEPENDENCY_ONE',
                        ]))
                        ->withVariableExports(VariablePlaceholderCollection::createCollection([
                            'EXPORT_ONE',
                        ]))
                        ->withClassDependencies(new ClassDependencyCollection([
                            new ClassDependency('CLASS_ONE'),
                        ]))),
                    new Statement('statement2', (new Metadata())
                        ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                            'DEPENDENCY_TWO',
                        ]))
                        ->withVariableExports(VariablePlaceholderCollection::createCollection([
                            'EXPORT_TWO',
                        ]))
                        ->withClassDependencies(new ClassDependencyCollection([
                            new ClassDependency('CLASS_TWO'),
                        ]))),
                ]),
                'expectedMetadata' => (new Metadata())
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                        'DEPENDENCY_ONE',
                        'DEPENDENCY_TWO',
                    ]))
                    ->withVariableExports(VariablePlaceholderCollection::createCollection([
                        'EXPORT_ONE',
                        'EXPORT_TWO',
                    ]))
                    ->withClassDependencies(new ClassDependencyCollection([
                        new ClassDependency('CLASS_ONE'),
                        new ClassDependency('CLASS_TWO'),
                    ])),
            ],
        ];
    }

    /**
     * @dataProvider noStatementLineListDataProvider
     */
    public function testAddClassDependenciesToLastStatementForNoStatementList(LineList $lineList)
    {
        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $lineList->addClassDependenciesToLastStatement($classDependencies);
        $this->assertEquals(new ClassDependencyCollection(), $lineList->getMetadata()->getClassDependencies());
    }

    public function noStatementLineListDataProvider(): array
    {
        return [
            'empty' => [
                'lineList' => new LineList(),
            ],
            'no statements' => [
                'lineList' => new LineList([
                    new EmptyLine(),
                    new Comment('comment'),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider addClassDependenciesToLastStatementDataProvider
     */
    public function testAddClassDependenciesToLastStatement(
        LineList $lineList,
        ClassDependencyCollection $classDependencies,
        $lastStatementIndex,
        ?ClassDependencyCollection $expectedCurrentClassDependencies = null,
        ?ClassDependencyCollection $expectedNewClassDependencies = null
    ) {
        $lines = $lineList->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentClassDependencies,
                $statement->getMetadata()->getClassDependencies()
            );

            $lineList->addClassDependenciesToLastStatement($classDependencies);

            $this->assertEquals(
                $expectedNewClassDependencies,
                $statement->getMetadata()->getClassDependencies()
            );
        } else {
            $this->fail('Last statement is not a statement');
        }
    }

    public function addClassDependenciesToLastStatementDataProvider(): array
    {
        return [
            'single statement only' => [
                'lineList' => new LineList([
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withClassDependencies(new ClassDependencyCollection([
                                new ClassDependency(Statement::class),
                            ]))
                    ),
                ]),
                'classDependencies' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                ]),
                'lastStatementIndex' => 0,
                'expectedCurrentClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(Statement::class),
                ]),
                'expectedNewClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(Statement::class),
                    new ClassDependency(ClassDependency::class),
                ]),
            ],
            'last line is only statement' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new EmptyLine(),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withClassDependencies(new ClassDependencyCollection([
                                new ClassDependency(Statement::class),
                            ]))
                    ),
                ]),
                'classDependencies' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                ]),
                'lastStatementIndex' => 2,
                'expectedCurrentClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(Statement::class),
                ]),
                'expectedNewClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(Statement::class),
                    new ClassDependency(ClassDependency::class),
                ]),
            ],
            'first line is only statement' => [
                'lineList' => new LineList([
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withClassDependencies(new ClassDependencyCollection([
                                new ClassDependency(Statement::class),
                            ]))
                    ),
                    new Comment('comment'),
                    new EmptyLine(),
                ]),
                'classDependencies' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                ]),
                'lastStatementIndex' => 0,
                'expectedCurrentClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(Statement::class),
                ]),
                'expectedNewClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(Statement::class),
                    new ClassDependency(ClassDependency::class),
                ]),
            ],
            'last statement is not last line' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withClassDependencies(new ClassDependencyCollection([
                                new ClassDependency(ClassDependencyCollection::class),
                            ]))
                    ),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withClassDependencies(new ClassDependencyCollection([
                                new ClassDependency(Statement::class),
                            ]))
                    ),
                    new EmptyLine(),
                ]),
                'classDependencies' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                ]),
                'lastStatementIndex' => 2,
                'expectedCurrentClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(Statement::class),
                ]),
                'expectedNewClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(Statement::class),
                    new ClassDependency(ClassDependency::class),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider noStatementLineListDataProvider
     */
    public function testAddVariableDependenciesToLastStatementForNoStatementList(LineList $lineList)
    {
        $variableDependencies = VariablePlaceholderCollection::createCollection(['PLACEHOLDER']);

        $lineList->addVariableDependenciesToLastStatement($variableDependencies);
        $this->assertEquals(new VariablePlaceholderCollection(), $lineList->getMetadata()->getVariableDependencies());
    }

    /**
     * @dataProvider addVariableDependenciesToLastStatementDataProvider
     */
    public function testAddVariableDependenciesToLastStatement(
        LineList $lineList,
        VariablePlaceholderCollection $variableDependencies,
        $lastStatementIndex,
        ?VariablePlaceholderCollection $expectedCurrentVariableDependencies = null,
        ?VariablePlaceholderCollection $expectedNewVariableDependencies = null
    ) {
        $lines = $lineList->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentVariableDependencies,
                $statement->getMetadata()->getVariableDependencies()
            );

            $lineList->addVariableDependenciesToLastStatement($variableDependencies);

            $this->assertEquals(
                $expectedNewVariableDependencies,
                $statement->getMetadata()->getVariableDependencies()
            );
        } else {
            $this->fail('Last statement is not a statement');
        }
    }

    public function addVariableDependenciesToLastStatementDataProvider(): array
    {
        return [
            'single statement only' => [
                'lineList' => new LineList([
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                ]),
                'variableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                ]),
                'lastStatementIndex' => 0,
                'expectedCurrentVariableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                ]),
                'expectedNewVariableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                    'PLACEHOLDER2',
                ]),
            ],
            'last line is only statement' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new EmptyLine(),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                ]),
                'variableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                ]),
                'lastStatementIndex' => 2,
                'expectedCurrentVariableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                ]),
                'expectedNewVariableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                    'PLACEHOLDER2',
                ]),
            ],
            'first line is only statement' => [
                'lineList' => new LineList([
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                    new Comment('comment'),
                    new EmptyLine(),
                ]),
                'variableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                ]),
                'lastStatementIndex' => 0,
                'expectedCurrentVariableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                ]),
                'expectedNewVariableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                    'PLACEHOLDER2',
                ]),
            ],
            'last statement is not last line' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER2',
                            ]))
                    ),
                    new EmptyLine(),
                ]),
                'variableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER3',
                ]),
                'lastStatementIndex' => 2,
                'expectedCurrentVariableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                ]),
                'expectedNewVariableDependencies' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                    'PLACEHOLDER3',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider noStatementLineListDataProvider
     */
    public function testAddVariableExportsToLastStatementForNoStatementList(LineList $lineList)
    {
        $variableExports = VariablePlaceholderCollection::createCollection(['PLACEHOLDER']);

        $lineList->addVariableExportsToLastStatement($variableExports);
        $this->assertEquals(new VariablePlaceholderCollection(), $lineList->getMetadata()->getVariableExports());
    }

    /**
     * @dataProvider addVariableExportsToLastStatementDataProvider
     */
    public function testAddVariableExportsToLastStatement(
        LineList $lineList,
        VariablePlaceholderCollection $variableExports,
        $lastStatementIndex,
        ?VariablePlaceholderCollection $expectedCurrentVariableDependencies = null,
        ?VariablePlaceholderCollection $expectedNewVariableDependencies = null
    ) {
        $lines = $lineList->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentVariableDependencies,
                $statement->getMetadata()->getVariableExports()
            );

            $lineList->addVariableExportsToLastStatement($variableExports);

            $this->assertEquals(
                $expectedNewVariableDependencies,
                $statement->getMetadata()->getVariableExports()
            );
        } else {
            $this->fail('Last statement is not a statement');
        }
    }

    public function addVariableExportsToLastStatementDataProvider(): array
    {
        return [
            'single statement only' => [
                'lineList' => new LineList([
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                ]),
                'variableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                ]),
                'lastStatementIndex' => 0,
                'expectedCurrentVariableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                ]),
                'expectedNewVariableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                    'PLACEHOLDER2',
                ]),
            ],
            'last line is only statement' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new EmptyLine(),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                ]),
                'variableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                ]),
                'lastStatementIndex' => 2,
                'expectedCurrentVariableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                ]),
                'expectedNewVariableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                    'PLACEHOLDER2',
                ]),
            ],
            'first line is only statement' => [
                'lineList' => new LineList([
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                    new Comment('comment'),
                    new EmptyLine(),
                ]),
                'variableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                ]),
                'lastStatementIndex' => 0,
                'expectedCurrentVariableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                ]),
                'expectedNewVariableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER1',
                    'PLACEHOLDER2',
                ]),
            ],
            'last statement is not last line' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER2',
                            ]))
                    ),
                    new EmptyLine(),
                ]),
                'variableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER3',
                ]),
                'lastStatementIndex' => 2,
                'expectedCurrentVariableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                ]),
                'expectedNewVariableExports' => VariablePlaceholderCollection::createCollection([
                    'PLACEHOLDER2',
                    'PLACEHOLDER3',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider mutateToLastStatementForNoStatementListDataProvider
     */
    public function testMutateToLastStatementForNoStatementList(LineList $lineList, array $expectedLines)
    {
        $lineList->mutateLastStatement(function () {
            return 'mutated!';
        });

        $this->assertEquals($expectedLines, $lineList->getLines());
    }

    public function mutateToLastStatementForNoStatementListDataProvider(): array
    {
        return [
            'empty' => [
                'lineList' => new LineList(),
                'expectedLines' => [],
            ],
            'no statements' => [
                'lineList' => new LineList([
                    new EmptyLine(),
                    new Comment('comment'),
                ]),
                'expectedLines' => [
                    '',
                    'comment',
                ],
            ],
        ];
    }

    /**
     * @dataProvider mutateLastStatementDataProvider
     */
    public function testMutateLastStatement(
        LineList $lineList,
        callable $mutator,
        $lastStatementIndex,
        string $expectedCurrentContent,
        string $expectedNewContent
    ) {
        $lines = $lineList->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentContent,
                $statement->getContent()
            );

            $lineList->mutateLastStatement($mutator);

            $this->assertEquals(
                $expectedNewContent,
                $statement->getContent()
            );
        } else {
            $this->fail('Last statement is not a statement');
        }
    }

    public function mutateLastStatementDataProvider(): array
    {
        return [
            'single statement only' => [
                'lineList' => new LineList([
                    new Statement('statement'),
                ]),
                'mutator' => function ($content) {
                    return $content . ' mutated';
                },
                'lastStatementIndex' => 0,
                'expectedCurrentContent' => 'statement',
                'expectedNewContent' => 'statement mutated',
            ],
            'last line is only statement' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new EmptyLine(),
                    new Statement('statement'),
                ]),
                'mutator' => function ($content) {
                    return $content . ' mutated';
                },
                'lastStatementIndex' => 2,
                'expectedCurrentContent' => 'statement',
                'expectedNewContent' => 'statement mutated',
            ],
            'first line is only statement' => [
                'lineList' => new LineList([
                    new Statement(
                        'statement',
                        (new Metadata())
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'PLACEHOLDER1',
                            ]))
                    ),
                    new Comment('comment'),
                    new EmptyLine(),
                ]),
                'mutator' => function ($content) {
                    return $content . ' mutated';
                },
                'lastStatementIndex' => 0,
                'expectedCurrentContent' => 'statement',
                'expectedNewContent' => 'statement mutated',
            ],
            'last statement is not last line' => [
                'lineList' => new LineList([
                    new Comment('comment'),
                    new Statement('statement1'),
                    new Statement('statement2'),
                    new EmptyLine(),
                ]),
                'mutator' => function ($content) {
                    return $content . ' mutated';
                },
                'lastStatementIndex' => 2,
                'expectedCurrentContent' => 'statement2',
                'expectedNewContent' => 'statement2 mutated',
            ],
        ];
    }
}
