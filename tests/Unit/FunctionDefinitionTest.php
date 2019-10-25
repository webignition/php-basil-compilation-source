<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\FunctionDefinition;
use webignition\BasilCompilationSource\SourceInterface;
use webignition\BasilCompilationSource\Statement;
use webignition\BasilCompilationSource\StatementList;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class FunctionDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name, SourceInterface $content, $arguments, array $expectedArguments)
    {
        $functionDefinition = new FunctionDefinition($name, $content, $arguments);

        $this->assertSame($name, $functionDefinition->getName());
        $this->assertSame($content, $functionDefinition->getContent());
        $this->assertEquals($expectedArguments, $functionDefinition->getArguments());
    }

    public function constructDataProvider(): array
    {
        return [
            'without arguments' => [
                'name' => 'withoutArguments',
                'content' => new StatementList([new Statement('statement')]),
                'arguments' => null,
                'expectedArguments' => [],
            ],
            'with arguments' => [
                'name' => 'withArguments',
                'content' => new StatementList([new Statement('statement')]),
                'arguments' => ['a', 'b', 'c'],
                'expectedArguments' => ['a', 'b', 'c'],
            ],
        ];
    }

    public function testGetStatements()
    {
        $content = 'statement';
        $expectedStatements = [$content];
        $functionDefinition = new FunctionDefinition('name', new StatementList([new Statement($content)]));

        $this->assertSame($expectedStatements, $functionDefinition->getStatements());
    }

    public function testGetStatementObjects()
    {
        $statement = new Statement('statement');
        $functionDefinition = new FunctionDefinition('name', new StatementList([$statement]));

        $this->assertEquals([$statement], $functionDefinition->getStatementObjects());
    }

    public function testMutateLastStatement()
    {
        $statement = new Statement('content');
        $functionDefinition = new FunctionDefinition('name', new StatementList([$statement]));

        $functionDefinition->mutateLastStatement(function (string $content) {
            return '!' . $content . '!';
        });

        $this->assertEquals('!content!', $statement->getContent());
    }

    public function testAddClassDependenciesToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(new ClassDependencyCollection([]), $statement->getMetadata()->getClassDependencies());

        $functionDefinition = new FunctionDefinition('name', new StatementList([$statement]));

        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $functionDefinition->addClassDependenciesToLastStatement($classDependencies);
        $this->assertEquals($classDependencies, $statement->getMetadata()->getClassDependencies());
    }

    public function testAddVariableDependenciesToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableDependencies()
        );

        $functionDefinition = new FunctionDefinition('name', new StatementList([$statement]));
        $variableDependencies = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $functionDefinition->addVariableDependenciesToLastStatement($variableDependencies);
        $this->assertEquals($variableDependencies, $statement->getMetadata()->getVariableDependencies());
    }

    public function testAddVariableExportsToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableExports()
        );

        $functionDefinition = new FunctionDefinition('name', new StatementList([$statement]));
        $variableExports = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $functionDefinition->addVariableExportsToLastStatement($variableExports);
        $this->assertEquals($variableExports, $statement->getMetadata()->getVariableExports());
    }
}
