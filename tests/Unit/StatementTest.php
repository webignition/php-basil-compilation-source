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
        $this->assertSame([$statement], $statement->getSources());
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

    public function testAddClassDependencies()
    {
        $statement = new Statement('statement');
        $this->assertEquals(new ClassDependencyCollection([]), $statement->getMetadata()->getClassDependencies());

        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $statement->addClassDependencies($classDependencies);
        $this->assertEquals($classDependencies, $statement->getMetadata()->getClassDependencies());
    }

    public function testAddVariableDependencies()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableDependencies()
        );
        $variableDependencies = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $statement->addVariableDependencies($variableDependencies);
        $this->assertEquals($variableDependencies, $statement->getMetadata()->getVariableDependencies());
    }

    public function testAddVariableExports()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableExports()
        );

        $variableExports = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $statement->addVariableExports($variableExports);
        $this->assertEquals($variableExports, $statement->getMetadata()->getVariableExports());
    }

    public function testIsStatement()
    {
        $statement = new Statement('statement');

        $this->assertTrue($statement->isStatement());
    }

    public function testIsComment()
    {
        $statement = new Statement('statement');

        $this->assertFalse($statement->isComment());
    }

    public function testIsEmpty()
    {
        $statement = new Statement('statement');

        $this->assertFalse($statement->isEmpty());
    }

    public function testToString()
    {
        $content = 'statement';
        $statement = new Statement($content);

        $this->assertSame($content, $statement->__toString());
    }

    public function testGetType()
    {
        $this->assertSame(Statement::TYPE, (new Statement(''))->getType());
    }

    public function testJsonSerialize()
    {
        $comment = new Statement('statement content');

        $this->assertSame(
            [
                'type' => 'statement',
                'content' => 'statement content',
            ],
            $comment->jsonSerialize()
        );
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
}
