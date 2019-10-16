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
        $this->assertEquals([], $compilableSource->getPredecessors());
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

    /**
     * @dataProvider prependStatementDataProvider
     */
    public function testPrependStatement(
        CompilableSourceInterface $source,
        int $index,
        string $content,
        CompilableSourceInterface $expectedSource
    ) {
        $source->prependStatement($index, $content);

        $this->assertEquals($expectedSource, $source);
    }

    public function prependStatementDataProvider(): array
    {
        return [
            'prepend first of one' => [
                'source' => (new CompilableSource())->withStatements(['statement']),
                'index' => 0,
                'content' => 'prepended ',
                'expectedSource' => (new CompilableSource())->withStatements(['prepended statement']),
            ],
            'prepend first of two' => [
                'source' => (new CompilableSource())->withStatements(['statement1', 'statement2']),
                'index' => 0,
                'content' => 'prepended ',
                'expectedSource' => (new CompilableSource())->withStatements(['prepended statement1', 'statement2']),
            ],
            'prepend last of one' => [
                'source' => (new CompilableSource())->withStatements(['statement']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedSource' => (new CompilableSource())->withStatements(['prepended statement']),
            ],
            'prepend last of two' => [
                'source' => (new CompilableSource())->withStatements(['statement1', 'statement2']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedSource' => (new CompilableSource())->withStatements(['statement1', 'prepended statement2']),
            ],
            'prepend last of three' => [
                'source' => (new CompilableSource())->withStatements(['statement1', 'statement2', 'statement3']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedSource' => (new CompilableSource())->withStatements([
                    'statement1',
                    'statement2',
                    'prepended statement3'
                ]),
            ],
        ];
    }

    /**
     * @dataProvider appendStatementDataProvider
     */
    public function testAppendStatement(
        CompilableSourceInterface $source,
        int $index,
        string $content,
        CompilableSourceInterface $expectedSource
    ) {
        $source->appendStatement($index, $content);

        $this->assertEquals($expectedSource, $source);
    }

    public function appendStatementDataProvider(): array
    {
        return [
            'append first of one' => [
                'source' => (new CompilableSource())->withStatements(['statement']),
                'index' => 0,
                'content' => ' appended',
                'expectedSource' => (new CompilableSource())->withStatements(['statement appended']),
            ],
            'append first of two' => [
                'source' => (new CompilableSource())->withStatements(['statement1', 'statement2']),
                'index' => 0,
                'content' => ' appended',
                'expectedSource' => (new CompilableSource())->withStatements(['statement1 appended', 'statement2']),
            ],
            'append last of one' => [
                'source' => (new CompilableSource())->withStatements(['statement']),
                'index' => -1,
                'content' => ' appended',
                'expectedSource' => (new CompilableSource())->withStatements(['statement appended']),
            ],
            'append last of two' => [
                'source' => (new CompilableSource())->withStatements(['statement1', 'statement2']),
                'index' => -1,
                'content' => ' appended',
                'expectedSource' => (new CompilableSource())->withStatements(['statement1', 'statement2 appended']),
            ],
            'append last of three' => [
                'source' => (new CompilableSource())->withStatements(['statement1', 'statement2', 'statement3']),
                'index' => -1,
                'content' => ' appended',
                'expectedSource' => (new CompilableSource())->withStatements([
                    'statement1',
                    'statement2',
                    'statement3 appended'
                ]),
            ],
        ];
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
