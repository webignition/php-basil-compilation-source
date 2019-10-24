<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\MetadataInterface;
use webignition\BasilCompilationSource\Statement;
use webignition\BasilCompilationSource\StatementList;
use webignition\BasilCompilationSource\StatementListInterface;
use webignition\BasilCompilationSource\Metadata;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class StatementListTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $statements = [
            new Statement('statement1'),
            new Statement('statement2'),
        ];

        $statementList = new StatementList($statements);

        $this->assertEquals($statements, $statementList->getStatementObjects());
    }

    /**
     * @dataProvider getStatementsDataProvider
     */
    public function testGetStatements(StatementListInterface $statementList, array $expectedStatements)
    {
        $this->assertEquals($expectedStatements, $statementList->getStatements());
    }

    public function getStatementsDataProvider(): array
    {
        return [
            'empty' => [
                'statementList' => new StatementList([]),
                'expectedStatements' => [],
            ],
            'non-empty' => [
                'statementList' => new StatementList([
                    new Statement('statement1'),
                    new Statement('statement2'),
                ]),
                'expectedStatements' => [
                    'statement1',
                    'statement2',
                ],
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
                'statementList' => new StatementList([]),
                'expectedMetadata' => new Metadata(),
            ],
            'no metadata' => [
                'statementList' => new StatementList([
                    new Statement('statement1'),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'statementList' => new StatementList([
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
     * @dataProvider getStatementObjectsDataProvider
     */
    public function testGetStatementObjects(StatementListInterface $statementList, array $expectedStatementObjects)
    {
        $this->assertEquals($expectedStatementObjects, $statementList->getStatementObjects());
    }

    public function getStatementObjectsDataProvider(): array
    {
        return [
            'empty' => [
                'statementList' => new StatementList([]),
                'expectedStatementObjects' => [],
            ],
            'non-empty' => [
                'statementList' => new StatementList([
                    new Statement('statement1'),
                    new Statement('statement2'),
                ]),
                'expectedStatementObjects' => [
                    new Statement('statement1'),
                    new Statement('statement2'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider prependStatementDataProvider
     */
    public function testPrependStatement(
        StatementListInterface $statementList,
        int $index,
        string $content,
        array $expectedStatements
    ) {
        $statementList->prependStatement($index, $content);

        $this->assertEquals($expectedStatements, $statementList->getStatements());
    }

    public function prependStatementDataProvider(): array
    {
        return [
            'prepend first of one' => [
                'statementList' => new StatementList([
                    new Statement('statement'),
                ]),
                'index' => 0,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement'],
            ],
            'prepend first of two' => [
                'statementList' => new StatementList([
                    new Statement('statement1'),
                    new Statement('statement2'),
                ]),
                'index' => 0,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement1', 'statement2'],
            ],
            'prepend last of one' => [
                'statementList' => new StatementList([
                    new Statement('statement'),
                ]),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['prepended statement'],
            ],
            'prepend last of two' => [
                'statementList' => new StatementList([
                    new Statement('statement1'),
                    new Statement('statement2'),
                ]),
                'index' => -1,
                'content' => 'prepended ',
                'expectedStatements' => ['statement1', 'prepended statement2'],
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
                'statementList' => new StatementList([
                    new Statement('statement'),
                ]),
                'index' => 0,
                'content' => ' appended',
                'expectedStatements' => ['statement appended'],
            ],
            'append first of two' => [
                'statementList' => new StatementList([
                    new Statement('statement1'),
                    new Statement('statement2'),
                ]),
                'index' => 0,
                'content' => ' appended',
                'expectedStatements' => ['statement1 appended', 'statement2'],
            ],
            'append last of one' => [
                'statementList' => new StatementList([
                    new Statement('statement'),
                ]),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement appended'],
            ],
            'append last of two' => [
                'statementList' => new StatementList([
                    new Statement('statement1'),
                    new Statement('statement2'),
                ]),
                'index' => -1,
                'content' => ' appended',
                'expectedStatements' => ['statement1', 'statement2 appended'],
            ],
        ];
    }

    public function testReplaceStatement()
    {
        $statement1 = new Statement('statement1');
        $statement2 = new Statement('statement2');

        $statementList = new StatementList([$statement1]);
        $this->assertEquals([$statement1], $statementList->getStatementObjects());

        $statementList->replaceStatement(0, $statement2);
        $this->assertEquals([$statement2], $statementList->getStatementObjects());
    }

    public function testGetStatement()
    {
        $statement1 = new Statement('statement1');
        $statement2 = new Statement('statement2');

        $statements = [
            $statement1,
            $statement2,
        ];

        $statementList = new StatementList($statements);

        $this->assertEquals($statement1, $statementList->getStatement(0));
        $this->assertEquals($statement1, $statementList->getStatement(-2));
        $this->assertEquals($statement2, $statementList->getStatement(1));
        $this->assertEquals($statement2, $statementList->getStatement(-1));

        $this->assertNull($statementList->getStatement(2));
        $this->assertNull($statementList->getStatement(-3));
    }

    public function testGetLastStatement()
    {
        $statement1 = new Statement('statement1');
        $statement2 = new Statement('statement2');

        $statementList = new StatementList([$statement1, $statement2]);
        $this->assertEquals($statement2, $statementList->getLastStatement());
    }

    public function testReplaceLastStatement()
    {
        $statement1 = new Statement('statement1');
        $statement2 = new Statement('statement2');
        $statement3 = new Statement('statement3');

        $statementList = new StatementList([$statement1, $statement2]);
        $this->assertEquals($statement2, $statementList->getLastStatement());

        $statementList->replaceLastStatement($statement3);
        $this->assertEquals($statement3, $statementList->getLastStatement());
    }
}
