<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\Source;
use webignition\BasilCompilationSource\SourceInterface;
use webignition\BasilCompilationSource\Metadata;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class SourceTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $compilableSource = new Source();

        $this->assertSame([], $compilableSource->getStatements());
        $this->assertEquals([], $compilableSource->getPredecessors());
        $this->assertEquals(new Metadata(), $compilableSource->getMetadata());
    }

    /**
     * @dataProvider withPredecessorsDataProvider
     */
    public function testWithPredecessors(
        SourceInterface $compilableSource,
        array $predecessors,
        SourceInterface $expectedCompilableSource
    ) {
        $mutatedCompilableSource = $compilableSource->withPredecessors($predecessors);

        $this->assertEquals($expectedCompilableSource->getStatements(), $mutatedCompilableSource->getStatements());
        $this->assertEquals(
            $expectedCompilableSource->getMetadata(),
            $mutatedCompilableSource->getMetadata()
        );
    }

    public function withPredecessorsDataProvider(): array
    {
        return [
            'empty, no predecessors' => [
                'compilableSource' => new Source(),
                'predecessors' => [],
                'expectedCompilableSource' => new Source(),
            ],
            'has statements, no predecessors' => [
                'compilableSource' => (new Source())
                    ->withStatements([
                        'statement1',
                    ]),
                'predecessors' => [],
                'expectedCompilableSource' => (new Source())
                    ->withStatements([
                        'statement1',
                    ]),
            ],
            'has metadata, no predecessors' => [
                'compilableSource' => (new Source())
                    ->withMetadata(
                        (new Metadata())
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
                'expectedCompilableSource' => (new Source())
                    ->withMetadata(
                        (new Metadata())
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
                'compilableSource' => (new Source())
                    ->withStatements([
                        'statement1',
                    ])
                    ->withMetadata(
                        (new Metadata())
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
                    (new Source())
                        ->withStatements([
                            'statement2',
                        ]),
                    (new Source())
                        ->withStatements([
                            'statement3',
                        ])
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
                ],
                'expectedCompilableSource' => (new Source())
                    ->withStatements([
                        'statement2',
                        'statement3',
                        'statement1',
                    ])
                    ->withMetadata(
                        (new Metadata())
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
        $compilableSource = new Source();
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
        $emptyCompilationMetadata = new Metadata();
        $compilationMetadata = (new Metadata())
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1']));

        $compilableSource = new Source();
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getMetadata());

        $compilableSource = $compilableSource->withMetadata($compilationMetadata);
        $this->assertEquals($compilationMetadata, $compilableSource->getMetadata());

        $compilableSource = $compilableSource->withMetadata($emptyCompilationMetadata);
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getMetadata());
    }

    /**
     * @dataProvider prependStatementDataProvider
     */
    public function testPrependStatement(
        SourceInterface $source,
        int $index,
        string $content,
        array $expectedStatements
    ) {
        $source->prependStatement($index, $content);

        $this->assertEquals($expectedStatements, $source->getStatements());
    }

    public function prependStatementDataProvider(): array
    {
        return [
            'prepend first of one' => [
                'source' => (new Source())->withStatements(['statement']),
                'index' => 0,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement'],
            ],
            'prepend first of two' => [
                'source' => (new Source())->withStatements(['statement1', 'statement2']),
                'index' => 0,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement1', 'statement2'],
            ],
            'prepend last of one' => [
                'source' => (new Source())->withStatements(['statement']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement'],
            ],
            'prepend last of two' => [
                'source' => (new Source())->withStatements(['statement1', 'statement2']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['statement1', 'prepended statement2'],
            ],
            'prepend last of three' => [
                'source' => (new Source())->withStatements(['statement1', 'statement2', 'statement3']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['statement1', 'statement2', 'prepended statement3'],
            ],
            'prepend last of three, all in predecessor' => [
                'source' => (new Source())
                    ->withPredecessors([
                        (new Source())->withStatements(['statement1', 'statement2', 'statement3'])
                    ]),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['statement1', 'statement2', 'prepended statement3'],
            ],
        ];
    }

    /**
     * @dataProvider appendStatementDataProvider
     */
    public function testAppendStatement(
        SourceInterface $source,
        int $index,
        string $content,
        array $expectedStatements
    ) {
        $source->appendStatement($index, $content);

        $this->assertEquals($expectedStatements, $source->getStatements());
    }

    public function appendStatementDataProvider(): array
    {
        return [
            'append first of one' => [
                'source' => (new Source())->withStatements(['statement']),
                'index' => 0,
                'content' => ' appended',
                'expectedStatements' => ['statement appended'],
            ],
            'append first of two' => [
                'source' => (new Source())->withStatements(['statement1', 'statement2']),
                'index' => 0,
                'content' => ' appended',
                'expectedStatements' => ['statement1 appended', 'statement2'],
            ],
            'append last of one' => [
                'source' => (new Source())->withStatements(['statement']),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement appended'],
            ],
            'append last of two' => [
                'source' => (new Source())->withStatements(['statement1', 'statement2']),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement1', 'statement2 appended'],
            ],
            'append last of three' => [
                'source' => (new Source())->withStatements(['statement1', 'statement2', 'statement3']),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement1', 'statement2', 'statement3 appended'],
            ],
            'append last of three, all in predecessor' => [
                'source' => (new Source())
                    ->withPredecessors([
                        (new Source())->withStatements(['statement1', 'statement2', 'statement3'])
                    ]),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement1', 'statement2', 'statement3 appended'],
            ],
        ];
    }

    /**
     * @dataProvider toStringDataProvider
     */
    public function testToString(SourceInterface $source, string $expectedString)
    {
        $this->assertSame($expectedString, (string) $source);
    }

    public function toStringDataProvider(): array
    {
        return [
            'empty' => [
                'source' => new Source(),
                'expectedString' => '',
            ],
            'statements only' => [
                'source' => (new Source())
                    ->withStatements(['statement1', 'statement2']),
                'expectedString' =>
                    "statement1;\n" .
                    "statement2;",
            ],
            'predecessors only' => [
                'source' => (new Source())
                    ->withPredecessors([
                        (new Source())->withStatements(['statement1', 'statement2']),
                        (new Source())->withStatements(['statement3', 'statement4']),
                    ]),
                'expectedString' =>
                    "statement1;\n" .
                    "statement2;\n" .
                    "statement3;\n" .
                    "statement4;",
            ],
            'predecessors and statements' => [
                'source' => (new Source())
                    ->withStatements(['statement5', 'statement6'])
                    ->withPredecessors([
                        (new Source())->withStatements(['statement1', 'statement2']),
                        (new Source())->withStatements(['statement3', 'statement4']),
                    ]),
                'expectedString' =>
                    "statement1;\n" .
                    "statement2;\n" .
                    "statement3;\n" .
                    "statement4;\n" .
                    "statement5;\n" .
                    "statement6;",
            ],
        ];
    }
}
