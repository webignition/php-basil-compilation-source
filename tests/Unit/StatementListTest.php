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
     * @dataProvider addStatementsDataProvider
     */
    public function testAddStatements(StatementList $statementList, array $statements, array $expectedStatements)
    {
        $statementList->addStatements($statements);

        $this->assertEquals($expectedStatements, $statementList->getStatements());
    }

    public function addStatementsDataProvider(): array
    {
        return [
            'empty list, empty statements' => [
                'statementList' => new StatementList([]),
                'statements' => [],
                'expectedStatements' => [],
            ],
        ];
    }

    /**
     * @dataProvider getStatementsDataProvider
     */
    public function testGetStatements(StatementList $statementList, array $expectedStatements)
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
    public function testGetMetadata(StatementList $statementList, MetadataInterface $expectedMetadata)
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
    public function testGetStatementObjects(StatementList $statementList, array $expectedStatementObjects)
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

    public function testAddClassDependenciesToLastStatement()
    {
        $statement = new Statement('statement2');
        $this->assertEquals(new ClassDependencyCollection([]), $statement->getMetadata()->getClassDependencies());

        $statementList = new StatementList([
            new Statement('statement1'),
            $statement,
        ]);

        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $statementList->addClassDependenciesToLastStatement($classDependencies);
        $this->assertEquals($classDependencies, $statement->getMetadata()->getClassDependencies());
    }

    public function testAddVariableDependenciesToLastStatement()
    {
        $statement = new Statement('statement2');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableDependencies()
        );

        $statementList = new StatementList([
            new Statement('statement1'),
            $statement,
        ]);

        $variableDependencies = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $statementList->addVariableDependenciesToLastStatement($variableDependencies);
        $this->assertEquals($variableDependencies, $statement->getMetadata()->getVariableDependencies());
    }

    public function testAddVariableExportsToLastStatement()
    {
        $statement = new Statement('statement2');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableExports()
        );

        $statementList = new StatementList([
            new Statement('statement1'),
            $statement,
        ]);

        $variableExports = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $statementList->addVariableExportsToLastStatement($variableExports);
        $this->assertEquals($variableExports, $statement->getMetadata()->getVariableExports());
    }

    public function testMutateLastStatement()
    {
        $statementList = new StatementList([
            new Statement('statement1'),
            new Statement('statement2'),
        ]);

        $this->assertEquals(['statement1', 'statement2'], $statementList->getStatements());

        $statementList->mutateLastStatement(function (string $content) {
            return '!' . $content . '!';
        });

        $this->assertEquals(['statement1', '!statement2!'], $statementList->getStatements());
    }
}
