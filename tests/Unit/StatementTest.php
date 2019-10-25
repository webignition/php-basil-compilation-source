<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\MetadataInterface;
use webignition\BasilCompilationSource\Statement;
use webignition\BasilCompilationSource\Metadata;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class StatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $content, $metadata, MetadataInterface $expectedMetadata)
    {
        $statement = new Statement($content, $metadata);

        $this->assertSame($content, $statement->getContent());
        $this->assertEquals($expectedMetadata, $statement->getMetadata());
    }

    public function constructDataProvider(): array
    {
        $metadata = (new Metadata())->withVariableDependencies(VariablePlaceholderCollection::createCollection([
            'PLACEHOLDER',
        ]));

        return [
            'without metadata' => [
                'content' => 'statement',
                'metadata' => null,
                'expectedMetadata' => new Metadata(),
            ],
            'with metadata' => [
                'content' => 'statement',
                'metadata' => $metadata,
                'expectedMetadata' => $metadata,
            ],
        ];
    }

    public function testGetStatements()
    {
        $content = 'statement';
        $expectedStatements = [$content];
        $statement = new Statement($content);

        $this->assertSame($expectedStatements, $statement->getStatements());
    }

    public function testGetStatementObjects()
    {
        $statement = new Statement('statement');

        $this->assertEquals([$statement], $statement->getStatementObjects());
    }

    public function testPrepend()
    {
        $statement = new Statement('content');
        $statement->prepend('prepended ');

        $this->assertEquals('prepended content', $statement->getContent());
    }

    public function testAppend()
    {
        $statement = new Statement('content');
        $statement->append(' appended');

        $this->assertEquals('content appended', $statement->getContent());
    }

    public function testMutate()
    {
        $statement = new Statement('content');
        $statement->mutate(function (string $content) {
            return '!' . $content . '!';
        });

        $this->assertEquals('!content!', $statement->getContent());
    }

    public function testMutateLastStatement()
    {
        $statement = new Statement('content');
        $statement->mutateLastStatement(function (string $content) {
            return '!' . $content . '!';
        });

        $this->assertEquals('!content!', $statement->getContent());
    }

    public function testAddClassDependenciesToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(new ClassDependencyCollection([]), $statement->getMetadata()->getClassDependencies());

        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $statement->addClassDependenciesToLastStatement($classDependencies);
        $this->assertEquals($classDependencies, $statement->getMetadata()->getClassDependencies());
    }

    public function testAddVariableDependenciesToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableDependencies()
        );
        $variableDependencies = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $statement->addVariableDependenciesToLastStatement($variableDependencies);
        $this->assertEquals($variableDependencies, $statement->getMetadata()->getVariableDependencies());
    }

    public function testAddVariableExportsToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableExports()
        );

        $variableExports = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $statement->addVariableExportsToLastStatement($variableExports);
        $this->assertEquals($variableExports, $statement->getMetadata()->getVariableExports());
    }

    public function testToString()
    {
        $content = 'statement';
        $statement = new Statement($content);

        $this->assertSame($content, $statement->__toString());
    }
}
