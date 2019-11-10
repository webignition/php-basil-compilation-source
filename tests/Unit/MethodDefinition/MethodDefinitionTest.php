<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\MethodDefinition;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\MethodDefinition\MethodDefinition;
use webignition\BasilCompilationSource\Block\Block;
use webignition\BasilCompilationSource\Line\Statement;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class MethodDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(
        string $name,
        Block $content,
        $arguments,
        array $expectedArguments
    ) {
        $methodDefinition = new MethodDefinition($name, $content, $arguments);

        $this->assertTrue($methodDefinition->isPublic());
        $this->assertFalse($methodDefinition->isStatic());
        $this->assertNull($methodDefinition->getReturnType());
        $this->assertSame($name, $methodDefinition->getName());
        $this->assertEquals($expectedArguments, $methodDefinition->getArguments());
        $this->assertEquals($content->getMetadata(), $methodDefinition->getMetadata());
    }

    public function constructDataProvider(): array
    {
        return [
            'without arguments' => [
                'name' => 'withoutArguments',
                'content' => new Block([new Statement('statement')]),
                'arguments' => null,
                'expectedArguments' => [],
            ],
            'with arguments' => [
                'name' => 'withArguments',
                'content' => new Block([new Statement('statement')]),
                'arguments' => ['a', 'b', 'c'],
                'expectedArguments' => ['a', 'b', 'c'],
            ],
        ];
    }

    public function testVisibility()
    {
        $methodDefinition = new MethodDefinition('name', new Block());
        $this->assertTrue($methodDefinition->isPublic());
        $this->assertFalse($methodDefinition->isProtected());
        $this->assertFalse($methodDefinition->isPrivate());
        $this->assertEquals(MethodDefinition::VISIBILITY_PUBLIC, $methodDefinition->getVisibility());

        $methodDefinition->setProtected();
        $this->assertFalse($methodDefinition->isPublic());
        $this->assertTrue($methodDefinition->isProtected());
        $this->assertFalse($methodDefinition->isPrivate());
        $this->assertEquals(MethodDefinition::VISIBILITY_PROTECTED, $methodDefinition->getVisibility());

        $methodDefinition->setPrivate();
        $this->assertFalse($methodDefinition->isPublic());
        $this->assertFalse($methodDefinition->isProtected());
        $this->assertTrue($methodDefinition->isPrivate());
        $this->assertEquals(MethodDefinition::VISIBILITY_PRIVATE, $methodDefinition->getVisibility());

        $methodDefinition->setPublic();
        $this->assertTrue($methodDefinition->isPublic());
        $this->assertFalse($methodDefinition->isProtected());
        $this->assertFalse($methodDefinition->isPrivate());
        $this->assertEquals(MethodDefinition::VISIBILITY_PUBLIC, $methodDefinition->getVisibility());
    }

    public function testAddLine()
    {
        $methodDefinition = new MethodDefinition(
            'name',
            new Block([
                new Statement('statement1'),
                new EmptyLine(),
                new Comment('comment1'),
            ])
        );

        $this->assertEquals(
            [
                new Statement('statement1'),
                new EmptyLine(),
                new Comment('comment1'),
            ],
            $methodDefinition->getLines()
        );

        $methodDefinition->addLine(new EmptyLine());

        $this->assertEquals(
            [
                new Statement('statement1'),
                new EmptyLine(),
                new Comment('comment1'),
                new EmptyLine(),
            ],
            $methodDefinition->getLines()
        );
    }

    public function testGetStatementObjects()
    {
        $statement = new Statement('statement');
        $methodDefinition = new MethodDefinition('name', new Block([$statement]));

        $this->assertEquals([$statement], $methodDefinition->getLines());
    }

    public function testMutateLastStatement()
    {
        $statement = new Statement('content');
        $methodDefinition = new MethodDefinition('name', new Block([$statement]));

        $methodDefinition->mutateLastStatement(function (string $content) {
            return '!' . $content . '!';
        });

        $this->assertEquals('!content!', $statement->getContent());
    }

    public function testAddClassDependenciesToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(new ClassDependencyCollection([]), $statement->getMetadata()->getClassDependencies());

        $methodDefinition = new MethodDefinition('name', new Block([$statement]));
        $classDependencies = new ClassDependencyCollection([
            new ClassDependency(ClassDependency::class),
        ]);

        $methodDefinition->addClassDependenciesToLastStatement($classDependencies);
        $this->assertEquals($classDependencies, $statement->getMetadata()->getClassDependencies());
    }

    public function testAddVariableDependenciesToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableDependencies()
        );

        $methodDefinition = new MethodDefinition('name', new Block([$statement]));
        $variableDependencies = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $methodDefinition->addVariableDependenciesToLastStatement($variableDependencies);
        $this->assertEquals($variableDependencies, $statement->getMetadata()->getVariableDependencies());
    }

    public function testAddVariableExportsToLastStatement()
    {
        $statement = new Statement('statement');
        $this->assertEquals(
            new VariablePlaceholderCollection([]),
            $statement->getMetadata()->getVariableExports()
        );

        $methodDefinition = new MethodDefinition('name', new Block([$statement]));
        $variableExports = VariablePlaceholderCollection::createCollection(['DEPENDENCY']);

        $methodDefinition->addVariableExportsToLastStatement($variableExports);
        $this->assertEquals($variableExports, $statement->getMetadata()->getVariableExports());
    }

    public function testSetReturnType()
    {
        $methodDefinition = new MethodDefinition('name', new Block());
        $this->assertNull($methodDefinition->getReturnType());

        $returnType = 'array';
        $methodDefinition->setReturnType($returnType);
        $this->assertEquals($returnType, $methodDefinition->getReturnType());
    }

    public function testIsStatic()
    {
        $methodDefinition = new MethodDefinition('name', new Block());
        $this->assertFalse($methodDefinition->isStatic());

        $methodDefinition->setStatic();
        $this->assertTrue($methodDefinition->isStatic());
    }

    public function testAddLinesFromBlock()
    {
        $block1 = new Block();
        $block1->addLine(new Comment('comment1'));
        $block1->addLine(new Statement('statement1'));

        $block2 = new Block();
        $block2->addLine(new Comment('comment2'));
        $block2->addLine(new Statement('statement2'));

        $method = new MethodDefinition('methodName', $block1);
        $method->addLinesFromBlock($block2);

        $this->assertEquals(
            [
                new Comment('comment1'),
                new Statement('statement1'),
                new Comment('comment2'),
                new Statement('statement2'),
            ],
            $method->getLines()
        );
    }
}
