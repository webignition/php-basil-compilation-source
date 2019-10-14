<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\CompilableSource;
use webignition\BasilCompilationSource\CompilableSourceInterface;
use webignition\BasilCompilationSource\CompilationMetadata;
use webignition\BasilCompilationSource\CompilationMetadataInterface;
use webignition\BasilCompilationSource\VariablePlaceholder;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class CompilableSourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(
        array $statements,
        array $predecessors,
        ?CompilationMetadataInterface $compilationMetadata,
        array $expectedStatements,
        CompilationMetadataInterface $expectedCompilationMetadata
    ) {
        $compilableSource = new CompilableSource($statements, $predecessors, $compilationMetadata);

        $this->assertSame($expectedStatements, $compilableSource->getStatements());
        $this->assertEquals($expectedCompilationMetadata, $compilableSource->getCompilationMetadata());
    }

    public function constructDataProvider(): array
    {
        return [
            'statements only' => [
                'statements' => [
                    'statement1',
                ],
                'predecessors' => [],
                'compilationMetadata' => null,
                'expectedStatements' => [
                    'statement1',
                ],
                'expectedCompilationMetadata' => new CompilationMetadata(),
            ],
            'statements and compilation metadata' => [
                'statements' => [
                    'statement1',
                ],
                'predecessors' => [],
                'compilationMetadata' => (new CompilationMetadata())
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1'])),
                'expectedStatements' => [
                    'statement1',
                ],
                'expectedCompilationMetadata' => (new CompilationMetadata())
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1'])),
            ],
            'statements, predecessors and compilation metadata' => [
                'statements' => [
                    'statement1',
                ],
                'predecessors' => [
                    new CompilableSource(
                        [
                            'statement2',
                            'statement3',
                        ]
                    )
                ],
                'compilationMetadata' => (new CompilationMetadata())
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1'])),
                'expectedStatements' => [
                    'statement2',
                    'statement3',
                    'statement1',
                ],
                'expectedCompilationMetadata' => (new CompilationMetadata())
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1'])),
            ],
        ];
    }

    public function testWithCompilationMetadata()
    {
        $emptyCompilationMetadata = new CompilationMetadata();
        $compilationMetadata = (new CompilationMetadata())
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1']));

        $compilableSource = new CompilableSource([]);
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getCompilationMetadata());

        $compilableSource = $compilableSource->withCompilationMetadata($compilationMetadata);
        $this->assertSame($compilationMetadata, $compilableSource->getCompilationMetadata());

        $compilableSource = $compilableSource->withCompilationMetadata($emptyCompilationMetadata);
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getCompilationMetadata());
    }

    public function testMergeCompilationData()
    {
        $emptyCompilationMetadata = new CompilationMetadata();
        $compilationMetadata1 = (new CompilationMetadata())
            ->withClassDependencies(new ClassDependencyCollection([
                new ClassDependency('class1'),
            ]));

        $compilationMetadata2 = (new CompilationMetadata())
            ->withClassDependencies(new ClassDependencyCollection([
                new ClassDependency('class2'),
            ]))
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1']))
            ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport1']));

        $compilableSource = new CompilableSource([]);
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getCompilationMetadata());

        $compilableSource = $compilableSource->mergeCompilationData([
            $compilationMetadata1,
            $compilationMetadata2,
        ]);

        $this->assertEquals(
            (new CompilationMetadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                    new ClassDependency('class2'),
                ]))
                ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1']))
                ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport1'])),
            $compilableSource->getCompilationMetadata()
        );
    }

    public function testToString()
    {
        $this->assertEquals('', (string) new CompilableSource([]));
        $this->assertEquals('statement1', (string) new CompilableSource(['statement1']));
        $this->assertEquals(
            'statement1' . "\n" . 'statement2',
            (string) new CompilableSource(['statement1', 'statement2'])
        );
    }

    /**
     * @dataProvider addPredecessorDataProvider
     */
    public function testAddPredecessor(
        CompilableSourceInterface $compilableSource,
        array $predecessors,
        CompilableSourceInterface $expectedCompilableSource
    ) {
        foreach ($predecessors as $predecessor) {
            $compilableSource->addPredecessor($predecessor);
        }

        $this->assertEquals($expectedCompilableSource->getStatements(), $compilableSource->getStatements());
        $this->assertEquals(
            $expectedCompilableSource->getCompilationMetadata(),
            $compilableSource->getCompilationMetadata()
        );
    }

    public function addPredecessorDataProvider(): array
    {
        return [
            'empty, no predecessors' => [
                'compilableSource' => new CompilableSource(),
                'predecessors' => [],
                'expectedCompilableSource' => new CompilableSource(),
            ],
            'non-empty, no predecessors' => [
                'compilableSource' => new CompilableSource(
                    [
                        'statement1',
                    ],
                    [],
                    (new CompilationMetadata())
                        ->withVariableDependencies(
                            new VariablePlaceholderCollection([
                                new VariablePlaceholder('DEPENDENCY_ONE')
                            ])
                        )
                ),
                'predecessors' => [],
                'expectedCompilableSource' => new CompilableSource(
                    [
                        'statement1',
                    ],
                    [],
                    (new CompilationMetadata())
                        ->withVariableDependencies(
                            new VariablePlaceholderCollection([
                                new VariablePlaceholder('DEPENDENCY_ONE')
                            ])
                        )
                ),
            ],
            'empty, with predecessors' => [
                'compilableSource' => new CompilableSource(),
                'predecessors' => [
                    new CompilableSource(
                        [
                            'statement1',
                        ],
                        [],
                        (new CompilationMetadata())
                            ->withVariableDependencies(
                                new VariablePlaceholderCollection([
                                    new VariablePlaceholder('DEPENDENCY_ONE')
                                ])
                            )
                    ),
                ],
                'expectedCompilableSource' => new CompilableSource(
                    [
                        'statement1',
                    ],
                    [],
                    (new CompilationMetadata())
                        ->withVariableDependencies(
                            new VariablePlaceholderCollection([
                                new VariablePlaceholder('DEPENDENCY_ONE')
                            ])
                        )
                ),
            ],
            'non-empty, with predecessors' => [
                'compilableSource' => new CompilableSource(
                    [
                        'statement1',
                    ],
                    [],
                    (new CompilationMetadata())
                        ->withVariableDependencies(
                            new VariablePlaceholderCollection([
                                new VariablePlaceholder('DEPENDENCY_ONE')
                            ])
                        )
                ),
                'predecessors' => [
                    new CompilableSource(
                        [
                            'statement2',
                        ],
                        [],
                        (new CompilationMetadata())
                            ->withVariableDependencies(
                                new VariablePlaceholderCollection([
                                    new VariablePlaceholder('DEPENDENCY_TWO')
                                ])
                            )
                    ),
                    new CompilableSource(
                        [
                            'statement3',
                        ],
                        [],
                        (new CompilationMetadata())
                            ->withVariableExports(
                                new VariablePlaceholderCollection([
                                    new VariablePlaceholder('EXPORT_ONE')
                                ])
                            )
                    ),
                ],
                'expectedCompilableSource' => new CompilableSource(
                    [
                        'statement2',
                        'statement3',
                        'statement1',
                    ],
                    [],
                    (new CompilationMetadata())
                        ->withVariableDependencies(
                            new VariablePlaceholderCollection([
                                new VariablePlaceholder('DEPENDENCY_ONE'),
                                new VariablePlaceholder('DEPENDENCY_TWO')
                            ])
                        )->withVariableExports(
                            new VariablePlaceholderCollection([
                                new VariablePlaceholder('EXPORT_ONE')
                            ])
                        )
                ),
            ],
        ];
    }
}
