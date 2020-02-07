<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Block;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\LineInterface;
use webignition\BasilCompilationSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\Line\Statement;
use webignition\BasilCompilationSource\Block\CodeBlock;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Line\StatementInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class CodeBlockTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $codeBlock = new CodeBlock([
            new Statement('statement1'),
            new EmptyLine(),
            new Comment('comment'),
            new ClassDependency(ClassDependency::class),
            new CodeBlock([
                new Statement('statement2'),
            ]),
            new ObjectMethodInvocation('object', 'methodName'),
        ]);

        $this->assertEquals(
            [
                new Statement('statement1'),
                new EmptyLine(),
                new Comment('comment'),
                new Statement('statement2'),
                new ObjectMethodInvocation('object', 'methodName'),
            ],
            $codeBlock->getLines()
        );
    }

    public function testAddLine()
    {
        $codeBlock = new CodeBlock();
        $this->assertEquals([], $codeBlock->getLines());

        $emptyLine = new EmptyLine();
        $codeBlock->addLine($emptyLine);
        $this->assertEquals(
            [
                $emptyLine,
            ],
            $codeBlock->getLines()
        );

        $comment = new Comment('comment');
        $codeBlock->addLine($comment);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
            ],
            $codeBlock->getLines()
        );

        $statement = new Statement('$x = $y');
        $codeBlock->addLine($statement);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
                $statement,
            ],
            $codeBlock->getLines()
        );

        $classDependency = new ClassDependency(ClassDependency::class);
        $codeBlock->addLine($classDependency);
        $this->assertEquals(
            [
                $emptyLine,
                $comment,
                $statement,
            ],
            $codeBlock->getLines()
        );
    }

    /**
     * @dataProvider getLinesDataProvider
     *
     * @param CodeBlock $codeBlock
     * @param LineInterface[] $expectedLines
     */
    public function testGetLines(CodeBlock $codeBlock, array $expectedLines)
    {
        $this->assertEquals($expectedLines, $codeBlock->getLines());
    }

    public function getLinesDataProvider(): array
    {
        return [
            'empty' => [
                'codeBlock' => new CodeBlock([]),
                'expectedLines' => [],
            ],
            'non-empty' => [
                'codeBlock' => new CodeBlock([
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
    public function testGetMetadata(CodeBlock $codeBlock, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $codeBlock->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'codeBlock' => new CodeBlock([]),
                'expectedMetadata' => new Metadata(),
            ],
            'non-statement lines' => [
                'codeBlock' => new CodeBlock([
                    new Comment('comment'),
                    new EmptyLine(),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'no metadata' => [
                'codeBlock' => new CodeBlock([
                    new Statement('statement1'),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata in statements' => [
                'codeBlock' => new CodeBlock([
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
            'has metadata in statement and in object method invocation' => [
                'codeBlock' => new CodeBlock([
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
                    (new ObjectMethodInvocation('object', 'methodName'))
                        ->withMetadata(
                            (new Metadata())
                                ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                    'DEPENDENCY_TWO',
                                ]))
                                ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                    'EXPORT_TWO',
                                ]))
                                ->withClassDependencies(new ClassDependencyCollection([
                                    new ClassDependency('CLASS_TWO'),
                                ]))
                        ),
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
    public function testAddClassDependenciesToLastStatementForEmptyBlock(CodeBlock $codeBlock)
    {
        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $codeBlock->addClassDependenciesToLastStatement($classDependencies);
        $this->assertEquals(new ClassDependencyCollection(), $codeBlock->getMetadata()->getClassDependencies());
    }

    public function emptyBlockDataProvider(): array
    {
        return [
            'empty' => [
                'codeBlock' => new CodeBlock(),
            ],
            'no statements' => [
                'codeBlock' => new CodeBlock([
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
        CodeBlock $codeBlock,
        ClassDependencyCollection $classDependencies,
        int $lastStatementIndex,
        ?ClassDependencyCollection $expectedCurrentClassDependencies = null,
        ?ClassDependencyCollection $expectedNewClassDependencies = null
    ) {
        $lines = $codeBlock->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentClassDependencies,
                $statement->getMetadata()->getClassDependencies()
            );

            $codeBlock->addClassDependenciesToLastStatement($classDependencies);

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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
    public function testAddVariableDependenciesToLastStatementForEmptyBlock(CodeBlock $codeBlock)
    {
        $variableDependencies = VariablePlaceholderCollection::createCollection(['PLACEHOLDER']);

        $codeBlock->addVariableDependenciesToLastStatement($variableDependencies);
        $this->assertEquals(new VariablePlaceholderCollection(), $codeBlock->getMetadata()->getVariableDependencies());
    }

    /**
     * @dataProvider addVariableDependenciesToLastStatementDataProvider
     */
    public function testAddVariableDependenciesToLastStatement(
        CodeBlock $codeBlock,
        VariablePlaceholderCollection $variableDependencies,
        int $lastStatementIndex,
        ?VariablePlaceholderCollection $expectedCurrentVariableDependencies = null,
        ?VariablePlaceholderCollection $expectedNewVariableDependencies = null
    ) {
        $lines = $codeBlock->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentVariableDependencies,
                $statement->getMetadata()->getVariableDependencies()
            );

            $codeBlock->addVariableDependenciesToLastStatement($variableDependencies);

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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
    public function testAddVariableExportsToLastStatementForEmptyBlock(CodeBlock $codeBlock)
    {
        $variableExports = VariablePlaceholderCollection::createCollection(['PLACEHOLDER']);

        $codeBlock->addVariableExportsToLastStatement($variableExports);
        $this->assertEquals(new VariablePlaceholderCollection(), $codeBlock->getMetadata()->getVariableExports());
    }

    /**
     * @dataProvider addVariableExportsToLastStatementDataProvider
     */
    public function testAddVariableExportsToLastStatement(
        CodeBlock $codeBlock,
        VariablePlaceholderCollection $variableExports,
        int $lastStatementIndex,
        ?VariablePlaceholderCollection $expectedCurrentVariableDependencies = null,
        ?VariablePlaceholderCollection $expectedNewVariableDependencies = null
    ) {
        $lines = $codeBlock->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentVariableDependencies,
                $statement->getMetadata()->getVariableExports()
            );

            $codeBlock->addVariableExportsToLastStatement($variableExports);

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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
     *
     * @param CodeBlock $codeBlock
     * @param LineInterface[] $expectedLines
     */
    public function testMutateToLastStatementForEmptyBlock(CodeBlock $codeBlock, array $expectedLines)
    {
        $codeBlock->mutateLastStatement(function () {
            return 'mutated!';
        });

        $this->assertEquals($expectedLines, $codeBlock->getLines());
    }

    public function mutateToLastStatementForEmptyBlockDataProvider(): array
    {
        return [
            'empty' => [
                'codeBlock' => new CodeBlock(),
                'expectedLines' => [],
            ],
            'no statements' => [
                'codeBlock' => new CodeBlock([
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
        CodeBlock $codeBlock,
        callable $mutator,
        int $lastStatementIndex,
        string $expectedCurrentContent,
        string $expectedNewContent
    ) {
        $lines = $codeBlock->getLines();
        $statement = $lines[$lastStatementIndex];

        if ($statement instanceof StatementInterface) {
            $this->assertEquals(
                $expectedCurrentContent,
                $statement->getContent()
            );

            $codeBlock->mutateLastStatement($mutator);

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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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
                'codeBlock' => new CodeBlock([
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

    public function testAddLinesFromBlock()
    {
        $block1 = new CodeBlock();
        $block1->addLine(new Comment('comment1'));
        $block1->addLine(new Statement('statement1'));

        $block2 = new CodeBlock();
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
