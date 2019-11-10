<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Block;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\Line\Statement;
use webignition\BasilCompilationSource\Block\Block;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Line\StatementInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class BlockTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $acceptedLines = [
            new Statement('statement1'),
            new Statement('statement2'),
            new EmptyLine(),
            new Comment('comment'),
        ];

        $block = new Block(array_merge($acceptedLines, [
            new ClassDependency(ClassDependency::class),
        ]));

        $this->assertSame($acceptedLines, $block->getLines());
    }

    public function testAddLine()
    {
        $block = new Block();
        $this->assertEquals([], $block->getLines());

        $emptyLine = new EmptyLine();
        $block->addLine($emptyLine);
        $this->assertEquals(
            [
                $emptyLine,
            ],
            $block->getLines()
        );

        $comment = new Comment('comment');
        $block->addLine($comment);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
            ],
            $block->getLines()
        );

        $statement = new Statement('$x = $y');
        $block->addLine($statement);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
                $statement,
            ],
            $block->getLines()
        );

        $classDependency = new ClassDependency(ClassDependency::class);
        $block->addLine($classDependency);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
                $statement,
            ],
            $block->getLines()
        );
    }

    /**
     * @dataProvider addLinesFromSourcesDataProvider
     */
    public function testAddLinesFromSources(Block $block, array $lines, array $expectedLines)
    {
        $block->addLinesFromSources($lines);

        $this->assertEquals($expectedLines, $block->getLines());
    }

    public function addLinesFromSourcesDataProvider(): array
    {
        return [
            'empty block, empty lines' => [
                'block' => new Block(),
                'lines' => [],
                'expectedLines' => [],
            ],
            'empty block, non-empty lines' => [
                'block' => new Block(),
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
            'non-empty block, non-empty lines' => [
                'block' => new Block([
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
    public function testGetLines(Block $block, array $expectedLines)
    {
        $this->assertEquals($expectedLines, $block->getLines());
    }

    /**
     * @dataProvider getLinesDataProvider
     */
    public function testGetContents(Block $block, array $expectedLineObjects)
    {
        $this->assertEquals($expectedLineObjects, $block->getSources());
    }

    public function getLinesDataProvider(): array
    {
        return [
            'empty' => [
                'block' => new Block([]),
                'expectedLines' => [],
            ],
            'non-empty' => [
                'block' => new Block([
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
    public function testGetMetadata(Block $block, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $block->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'block' => new Block([]),
                'expectedMetadata' => new Metadata(),
            ],
            'non-statement lines' => [
                'block' => new Block([
                    new Comment('comment'),
                    new EmptyLine(),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'no metadata' => [
                'block' => new Block([
                    new Statement('statement1'),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'block' => new Block([
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
     * @dataProvider emptyBlockDataProvider
     */
    public function testAddClassDependenciesToLastStatementForEmptyBlock(Block $block)
    {
        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $block->addClassDependenciesToLastStatement($classDependencies);
        $this->assertEquals(new ClassDependencyCollection(), $block->getMetadata()->getClassDependencies());
    }

    public function emptyBlockDataProvider(): array
    {
        return [
            'empty' => [
                'block' => new Block(),
            ],
            'no statements' => [
                'block' => new Block([
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
        Block $block,
        ClassDependencyCollection $classDependencies,
        $lastStatementIndex,
        ?ClassDependencyCollection $expectedCurrentClassDependencies = null,
        ?ClassDependencyCollection $expectedNewClassDependencies = null
    ) {
        $lines = $block->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentClassDependencies,
                $statement->getMetadata()->getClassDependencies()
            );

            $block->addClassDependenciesToLastStatement($classDependencies);

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
                'block' => new Block([
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
                'block' => new Block([
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
                'block' => new Block([
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
                'block' => new Block([
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
     * @dataProvider emptyBlockDataProvider
     */
    public function testAddVariableDependenciesToLastStatementForEmptyBlock(Block $block)
    {
        $variableDependencies = VariablePlaceholderCollection::createCollection(['PLACEHOLDER']);

        $block->addVariableDependenciesToLastStatement($variableDependencies);
        $this->assertEquals(new VariablePlaceholderCollection(), $block->getMetadata()->getVariableDependencies());
    }

    /**
     * @dataProvider addVariableDependenciesToLastStatementDataProvider
     */
    public function testAddVariableDependenciesToLastStatement(
        Block $block,
        VariablePlaceholderCollection $variableDependencies,
        $lastStatementIndex,
        ?VariablePlaceholderCollection $expectedCurrentVariableDependencies = null,
        ?VariablePlaceholderCollection $expectedNewVariableDependencies = null
    ) {
        $lines = $block->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentVariableDependencies,
                $statement->getMetadata()->getVariableDependencies()
            );

            $block->addVariableDependenciesToLastStatement($variableDependencies);

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
                'block' => new Block([
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
                'block' => new Block([
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
                'block' => new Block([
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
                'block' => new Block([
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
     * @dataProvider emptyBlockDataProvider
     */
    public function testAddVariableExportsToLastStatementForEmptyBlock(Block $block)
    {
        $variableExports = VariablePlaceholderCollection::createCollection(['PLACEHOLDER']);

        $block->addVariableExportsToLastStatement($variableExports);
        $this->assertEquals(new VariablePlaceholderCollection(), $block->getMetadata()->getVariableExports());
    }

    /**
     * @dataProvider addVariableExportsToLastStatementDataProvider
     */
    public function testAddVariableExportsToLastStatement(
        Block $block,
        VariablePlaceholderCollection $variableExports,
        $lastStatementIndex,
        ?VariablePlaceholderCollection $expectedCurrentVariableDependencies = null,
        ?VariablePlaceholderCollection $expectedNewVariableDependencies = null
    ) {
        $lines = $block->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentVariableDependencies,
                $statement->getMetadata()->getVariableExports()
            );

            $block->addVariableExportsToLastStatement($variableExports);

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
                'block' => new Block([
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
                'block' => new Block([
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
                'block' => new Block([
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
                'block' => new Block([
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
     * @dataProvider mutateToLastStatementForEmptyBlockDataProvider
     */
    public function testMutateToLastStatementForEmptyBlock(Block $block, array $expectedLines)
    {
        $block->mutateLastStatement(function () {
            return 'mutated!';
        });

        $this->assertEquals($expectedLines, $block->getLines());
    }

    public function mutateToLastStatementForEmptyBlockDataProvider(): array
    {
        return [
            'empty' => [
                'block' => new Block(),
                'expectedLines' => [],
            ],
            'no statements' => [
                'block' => new Block([
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
        Block $block,
        callable $mutator,
        $lastStatementIndex,
        string $expectedCurrentContent,
        string $expectedNewContent
    ) {
        $lines = $block->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentContent,
                $statement->getContent()
            );

            $block->mutateLastStatement($mutator);

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
                'block' => new Block([
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
                'block' => new Block([
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
                'block' => new Block([
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
                'block' => new Block([
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

    /**
     * @dataProvider fromContentDataProvider
     */
    public function testFromContent(array $content, Block $expectedBlock)
    {
        $this->assertEquals($expectedBlock, Block::fromContent($content));
    }

    public function fromContentDataProvider(): array
    {
        return [
            'empty' => [
                'content' => [],
                'expectedBlock' => new Block(),
            ],
            'non-empty' => [
                'content' => [
                    '//comment without leading whitespace',
                    '// comment with single leading whitespace',
                    '//       comment with multiple leading whitespace',
                    '',
                    '$x = $y',
                    'use Foo',
                ],
                'expectedBlock' => new Block([
                    new Comment('comment without leading whitespace'),
                    new Comment('comment with single leading whitespace'),
                    new Comment('comment with multiple leading whitespace'),
                    new EmptyLine(),
                    new Statement('$x = $y')
                ]),
            ],
        ];
    }

    public function testAddLinesFromBlock()
    {
        $block1 = new Block();
        $block1->addLine(new Comment('comment1'));
        $block1->addLine(new Statement('statement1'));

        $block2 = new Block();
        $block2->addLine(new Comment('comment2'));
        $block2->addLine(new Statement('statement2'));
        $block2->addLinesFromBlock($block1);

        $this->assertEquals(
            [
                new Comment('comment2'),
                new Statement('statement2'),
                new Comment('comment1'),
                new Statement('statement1'),
            ],
            $block2->getLines()
        );
    }
}
