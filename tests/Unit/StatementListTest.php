<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\MetadataInterface;
use webignition\BasilCompilationSource\StatementList;
use webignition\BasilCompilationSource\StatementListInterface;
use webignition\BasilCompilationSource\Metadata;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class StatementListTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $compilableSource = new StatementList();

        $this->assertSame([], $compilableSource->getStatements());
        $this->assertEquals([], $compilableSource->getPredecessors());
        $this->assertEquals(new Metadata(), $compilableSource->getMetadata());
    }

    /**
     * @dataProvider getStatementsDataProvider
     */
    public function testGetStatements(StatementListInterface $statementList, array $expectedStatements)
    {
        $this->assertSame($expectedStatements, $statementList->getStatements());
    }

    public function getStatementsDataProvider(): array
    {
        return [
            'empty' => [
                'statementList' => new StatementList(),
                'expectedStatements' => [],
            ],
            'statements only' => [
                'statementList' => (new StatementList())
                    ->withStatements(['statement1']),
                'expectedStatements' => ['statement1']
            ],
            'predecessors only' => [
                'statementList' => (new StatementList())
                    ->withPredecessors([
                        (new StatementList())->withStatements(['statement1']),
                        (new StatementList())->withStatements(['statement2']),
                    ]),
                'expectedStatements' => ['statement1', 'statement2']
            ],
            'predecessors and statements' => [
                'statementList' => (new StatementList())
                    ->withStatements(['statement3'])
                    ->withPredecessors([
                        (new StatementList())->withStatements(['statement1']),
                        (new StatementList())->withStatements(['statement2']),
                    ]),
                'expectedStatements' => ['statement1', 'statement2', 'statement3']
            ],
        ];
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(StatementListInterface $statementList, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $statementList->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'statementList' => new StatementList(),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata, no predecessors' => [
                'statementList' => (new StatementList())
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
                'expectedMetadata' => (new Metadata())
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                        'DEPENDENCY_ONE',
                    ]))
                    ->withVariableExports(VariablePlaceholderCollection::createCollection([
                        'EXPORT_ONE',
                    ]))
                    ->withClassDependencies(new ClassDependencyCollection([
                        new ClassDependency('CLASS_ONE'),
                    ])),
            ],
            'has metadata, has predecessors' => [
                'statementList' => (new StatementList())
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
                    )->withPredecessors([
                        (new StatementList())
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

    public function testWithPredecessors()
    {
        $statementList = new StatementList();
        $this->assertSame([], $statementList->getPredecessors());

        $predecessors = [
            new StatementList(),
        ];

        $statementList = $statementList->withPredecessors($predecessors);
        $this->assertSame($predecessors, $statementList->getPredecessors());
    }

    public function testWithStatements()
    {
        $compilableSource = new StatementList();
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

        $compilableSource = new StatementList();
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
        StatementListInterface $source,
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
                'statementList' => (new StatementList())->withStatements(['statement']),
                'index' => 0,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement'],
            ],
            'prepend first of two' => [
                'statementList' => (new StatementList())->withStatements(['statement1', 'statement2']),
                'index' => 0,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement1', 'statement2'],
            ],
            'prepend last of one' => [
                'statementList' => (new StatementList())->withStatements(['statement']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement'],
            ],
            'prepend last of two' => [
                'statementList' => (new StatementList())->withStatements(['statement1', 'statement2']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['statement1', 'prepended statement2'],
            ],
            'prepend last of three' => [
                'statementList' => (new StatementList())->withStatements(['statement1', 'statement2', 'statement3']),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['statement1', 'statement2', 'prepended statement3'],
            ],
            'prepend last of three, all in predecessor' => [
                'statementList' => (new StatementList())
                    ->withPredecessors([
                        (new StatementList())->withStatements(['statement1', 'statement2', 'statement3'])
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
        StatementListInterface $source,
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
                'statementList' => (new StatementList())->withStatements(['statement']),
                'index' => 0,
                'content' => ' appended',
                'expectedStatements' => ['statement appended'],
            ],
            'append first of two' => [
                'statementList' => (new StatementList())->withStatements(['statement1', 'statement2']),
                'index' => 0,
                'content' => ' appended',
                'expectedStatements' => ['statement1 appended', 'statement2'],
            ],
            'append last of one' => [
                'statementList' => (new StatementList())->withStatements(['statement']),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement appended'],
            ],
            'append last of two' => [
                'statementList' => (new StatementList())->withStatements(['statement1', 'statement2']),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement1', 'statement2 appended'],
            ],
            'append last of three' => [
                'statementList' => (new StatementList())->withStatements(['statement1', 'statement2', 'statement3']),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement1', 'statement2', 'statement3 appended'],
            ],
            'append last of three, all in predecessor' => [
                'statementList' => (new StatementList())
                    ->withPredecessors([
                        (new StatementList())->withStatements(['statement1', 'statement2', 'statement3'])
                    ]),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement1', 'statement2', 'statement3 appended'],
            ],
        ];
    }

    /**
     * @dataProvider toCodeDataProvider
     */
    public function testToCode(StatementListInterface $source, string $expectedString)
    {
        $this->assertSame($expectedString, $source->toCode());
    }

    public function toCodeDataProvider(): array
    {
        return [
            'empty' => [
                'statementList' => new StatementList(),
                'expectedString' => '',
            ],
            'statements only' => [
                'statementList' => (new StatementList())
                    ->withStatements(['statement1', 'statement2']),
                'expectedString' =>
                    "statement1;\n" .
                    "statement2;",
            ],
            'predecessors only' => [
                'statementList' => (new StatementList())
                    ->withPredecessors([
                        (new StatementList())->withStatements(['statement1', 'statement2']),
                        (new StatementList())->withStatements(['statement3', 'statement4']),
                    ]),
                'expectedString' =>
                    "statement1;\n" .
                    "statement2;\n" .
                    "statement3;\n" .
                    "statement4;",
            ],
            'predecessors and statements' => [
                'statementList' => (new StatementList())
                    ->withStatements(['statement5', 'statement6'])
                    ->withPredecessors([
                        (new StatementList())->withStatements(['statement1', 'statement2']),
                        (new StatementList())->withStatements(['statement3', 'statement4']),
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

    /**
     * @dataProvider toStringDataProvider
     */
    public function testToString(StatementListInterface $source, string $expectedString)
    {
        $this->assertSame($expectedString, (string) $source);
    }

    public function toStringDataProvider(): array
    {
        return [
            'empty' => [
                'statementList' => new StatementList(),
                'expectedString' => '',
            ],
            'statements only' => [
                'statementList' => (new StatementList())
                    ->withStatements(['statement1', 'statement2']),
                'expectedString' =>
                    "statement1\n" .
                    "statement2",
            ],
            'predecessors only' => [
                'statementList' => (new StatementList())
                    ->withPredecessors([
                        (new StatementList())->withStatements(['statement1', 'statement2']),
                        (new StatementList())->withStatements(['statement3', 'statement4']),
                    ]),
                'expectedString' =>
                    "statement1\n" .
                    "statement2\n" .
                    "statement3\n" .
                    "statement4",
            ],
            'predecessors and statements' => [
                'statementList' => (new StatementList())
                    ->withStatements(['statement5', 'statement6'])
                    ->withPredecessors([
                        (new StatementList())->withStatements(['statement1', 'statement2']),
                        (new StatementList())->withStatements(['statement3', 'statement4']),
                    ]),
                'expectedString' =>
                    "statement1\n" .
                    "statement2\n" .
                    "statement3\n" .
                    "statement4\n" .
                    "statement5\n" .
                    "statement6",
            ],
        ];
    }
}
