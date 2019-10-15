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
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class CompilableSourceTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $compilableSource = new CompilableSource();

        $this->assertSame([], $compilableSource->getStatements());
        $this->assertEquals(new CompilationMetadata(), $compilableSource->getCompilationMetadata());
    }

    /**
     * @dataProvider withPredecessorsDataProvider
     */
    public function testWithPredecessors(
        CompilableSourceInterface $compilableSource,
        array $predecessors,
        CompilableSourceInterface $expectedCompilableSource
    ) {
        $mutatedCompilableSource = $compilableSource->withPredecessors($predecessors);

        $this->assertEquals($expectedCompilableSource->getStatements(), $mutatedCompilableSource->getStatements());
        $this->assertEquals(
            $expectedCompilableSource->getCompilationMetadata(),
            $mutatedCompilableSource->getCompilationMetadata()
        );
    }

    public function withPredecessorsDataProvider(): array
    {
        return [
            'empty, no predecessors' => [
                'compilableSource' => new CompilableSource(),
                'predecessors' => [],
                'expectedCompilableSource' => new CompilableSource(),
            ],
            'has statements, no predecessors' => [
                'compilableSource' => (new CompilableSource())
                    ->withStatements([
                        'statement1',
                    ]),
                'predecessors' => [],
                'expectedCompilableSource' => (new CompilableSource())
                    ->withStatements([
                        'statement1',
                    ]),
            ],
            'has metadata, no predecessors' => [
                'compilableSource' => (new CompilableSource())
                    ->withCompilationMetadata(
                        (new CompilationMetadata())
                            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                'DEPENDENCY_ONE',
                            ]))
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'EXPORT_ONE',
                            ]))
                            ->withClassDependencies(new ClassDependencyCollection([
                                new ClassDependency('CLASS_ONE'),
                            ]))
                    ),
                'predecessors' => [],
                'expectedCompilableSource' => (new CompilableSource())
                    ->withCompilationMetadata(
                        (new CompilationMetadata())
                            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                'DEPENDENCY_ONE',
                            ]))
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'EXPORT_ONE',
                            ]))
                            ->withClassDependencies(new ClassDependencyCollection([
                                new ClassDependency('CLASS_ONE'),
                            ]))
                    ),
            ],
            'has statements, has metadata, has predecessors' => [
                'compilableSource' => (new CompilableSource())
                    ->withStatements([
                        'statement1',
                    ])
                    ->withCompilationMetadata(
                        (new CompilationMetadata())
                            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                'DEPENDENCY_ONE',
                            ]))
                            ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                'EXPORT_ONE',
                            ]))
                            ->withClassDependencies(new ClassDependencyCollection([
                                new ClassDependency('CLASS_ONE'),
                            ]))
                    ),
                'predecessors' => [
                    (new CompilableSource())
                        ->withStatements([
                            'statement2',
                        ]),
                    (new CompilableSource())
                        ->withStatements([
                            'statement3',
                        ])
                        ->withCompilationMetadata(
                            (new CompilationMetadata())
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
                ],
                'expectedCompilableSource' => (new CompilableSource())
                    ->withStatements([
                        'statement2',
                        'statement3',
                        'statement1',
                    ])
                    ->withCompilationMetadata(
                        (new CompilationMetadata())
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
                            ]))
                    ),
            ],
        ];
    }

    public function testWithStatements()
    {
        $compilableSource = new CompilableSource();
        $this->assertSame([], $compilableSource->getStatements());

        $statements = [
            'statement1',
        ];

        $compilableSource = $compilableSource->withStatements($statements);
        $this->assertSame($statements, $compilableSource->getStatements());

        $compilableSource = $compilableSource->withStatements([]);
        $this->assertSame([], $compilableSource->getStatements());
    }

    public function testWithCompilationMetadata()
    {
        $emptyCompilationMetadata = new CompilationMetadata();
        $compilationMetadata = (new CompilationMetadata())
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1']));

        $compilableSource = new CompilableSource();
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getCompilationMetadata());

        $compilableSource = $compilableSource->withCompilationMetadata($compilationMetadata);
        $this->assertEquals($compilationMetadata, $compilableSource->getCompilationMetadata());

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

        $compilableSource = new CompilableSource();
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
        $this->assertEquals('', (string) new CompilableSource());

        $this->assertEquals(
            'statement1',
            (string) (new CompilableSource())->withStatements(['statement1'])
        );

        $this->assertEquals(
            'statement1' . "\n" . 'statement2',
            (string) (new CompilableSource())->withStatements(['statement1', 'statement2'])
        );
    }
}
