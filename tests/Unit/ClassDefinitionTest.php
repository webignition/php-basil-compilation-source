<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDefinition;
use webignition\BasilCompilationSource\ClassDefinitionInterface;
use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\Metadata;
use webignition\BasilCompilationSource\MetadataInterface;
use webignition\BasilCompilationSource\MethodDefinition;
use webignition\BasilCompilationSource\Statement;
use webignition\BasilCompilationSource\LineList;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class ClassDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name, array $methods, array $expectedMethods)
    {
        $classDefinition = new ClassDefinition($name, $methods);

        $this->assertSame($name, $classDefinition->getName());
        $this->assertEquals($classDefinition->getMethods(), $expectedMethods);
        $this->assertEquals($classDefinition->getSources(), $expectedMethods);
    }

    public function constructDataProvider(): array
    {
        return [
            'empty methods' => [
                'name' => 'name1',
                'methods' => [],
                'expectedMethods' => [],
            ],
            'invalid and valid methods' => [
                'name' => 'name2',
                'methods' => [
                    1,
                    new MethodDefinition('method1', new LineList()),
                    'string',
                    new MethodDefinition('method2', new LineList()),
                    true,
                    new MethodDefinition('method3', new LineList()),
                    new \stdClass(),
                ],
                'expectedMethods' => [
                    new MethodDefinition('method1', new LineList()),
                    new MethodDefinition('method2', new LineList()),
                    new MethodDefinition('method3', new LineList()),
                ],
            ],
        ];
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ClassDefinitionInterface $classDefinition, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $classDefinition->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'no functions' => [
                'classDefinition' => new ClassDefinition('name', []),
                'expectedMetadata' => new Metadata(),
            ],
            'single function, no lines' => [
                'classDefinition' => new ClassDefinition('name', [
                    new MethodDefinition('method', new LineList()),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'single function, has lines, no metadata' => [
                'classDefinition' => new ClassDefinition('name', [
                    new MethodDefinition('method', new LineList([
                        new Statement('statement'),
                    ])),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'single function, has lines, has metadata' => [
                'classDefinition' => new ClassDefinition('name', [
                    new MethodDefinition('method', new LineList([
                        new Statement(
                            'statement',
                            (new Metadata())
                                ->withClassDependencies(new ClassDependencyCollection([
                                    new ClassDependency(ClassDefinition::class),
                                ]))
                        ),
                    ])),
                ]),
                'expectedMetadata' => (new Metadata())
                    ->withClassDependencies(new ClassDependencyCollection([
                        new ClassDependency(ClassDefinition::class),
                    ]))
            ],
            'many functions with metadata' => [
                'classDefinition' => new ClassDefinition('name', [
                    new MethodDefinition('method', new LineList([
                        new Statement(
                            'statement',
                            (new Metadata())
                                ->withClassDependencies(new ClassDependencyCollection([
                                    new ClassDependency('ClassDependency1'),
                                ]))
                                ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                    'variableDependency1',
                                ]))
                                ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                    'variableExport1',
                                ]))
                        ),
                        new Statement(
                            'statement',
                            (new Metadata())
                                ->withClassDependencies(new ClassDependencyCollection([
                                    new ClassDependency('ClassDependency2'),
                                ]))
                                ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                    'variableDependency2',
                                ]))
                                ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                    'variableExport2',
                                ]))
                        ),
                    ])),
                    new MethodDefinition('method', new LineList([
                        new Statement(
                            'statement',
                            (new Metadata())
                                ->withClassDependencies(new ClassDependencyCollection([
                                    new ClassDependency('ClassDependency3'),
                                ]))
                                ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                                    'variableDependency3',
                                ]))
                                ->withVariableExports(VariablePlaceholderCollection::createCollection([
                                    'variableExport3',
                                ]))
                        ),
                    ])),
                ]),
                'expectedMetadata' => (new Metadata())
                    ->withClassDependencies(new ClassDependencyCollection([
                        new ClassDependency('ClassDependency1'),
                        new ClassDependency('ClassDependency2'),
                        new ClassDependency('ClassDependency3'),
                    ]))
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                        'variableDependency1',
                        'variableDependency2',
                        'variableDependency3',
                    ]))
                    ->withVariableExports(VariablePlaceholderCollection::createCollection([
                        'variableExport1',
                        'variableExport2',
                        'variableExport3',
                    ]))
            ],
        ];
    }
}
