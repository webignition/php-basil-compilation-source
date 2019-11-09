<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\ClassDefinition;

use webignition\BasilCompilationSource\ClassDefinition\ClassDefinition;
use webignition\BasilCompilationSource\ClassDefinition\ClassDefinitionInterface;
use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\MethodDefinition\MethodDefinition;
use webignition\BasilCompilationSource\MethodDefinition\MethodDefinitionInterface;
use webignition\BasilCompilationSource\Line\Statement;
use webignition\BasilCompilationSource\Block\Block;
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
                    new MethodDefinition('method1', new Block()),
                    'string',
                    new MethodDefinition('method2', new Block()),
                    true,
                    new MethodDefinition('method3', new Block()),
                    new \stdClass(),
                ],
                'expectedMethods' => [
                    'method1' => new MethodDefinition('method1', new Block()),
                    'method2' => new MethodDefinition('method2', new Block()),
                    'method3' => new MethodDefinition('method3', new Block()),
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
                    new MethodDefinition('method', new Block()),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'single function, has lines, no metadata' => [
                'classDefinition' => new ClassDefinition('name', [
                    new MethodDefinition('method', new Block([
                        new Statement('statement'),
                    ])),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'single function, has lines, has metadata' => [
                'classDefinition' => new ClassDefinition('name', [
                    new MethodDefinition('method', new Block([
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
                    new MethodDefinition('method1', new Block([
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
                    new MethodDefinition('method2', new Block([
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

    public function testGetMethodNoMethodExists()
    {
        $classDefinition = new ClassDefinition('Classname', []);

        $this->assertNull($classDefinition->getMethod('methodName'));
    }

    /**
     * @dataProvider getMethodDataProvider
     */
    public function testGetMethod(
        ClassDefinitionInterface $classDefinition,
        string $name,
        MethodDefinitionInterface $expectedMethod
    ) {
        $method = $classDefinition->getMethod($name);

        if ($method instanceof MethodDefinitionInterface) {
            $this->assertSame($name, $method->getName());
            $this->assertEquals($expectedMethod->getLines(), $method->getLines());
        }
    }

    public function getMethodDataProvider(): array
    {
        return [
            'method one of one' => [
                'classDefinition' => new ClassDefinition('ClassName', [
                    new MethodDefinition('methodOne', new Block([
                        new Comment('methodOne comment'),
                    ])),
                ]),
                'name' => 'methodOne',
                'expectedMethod' => new MethodDefinition('methodOne', new Block([
                    new Comment('methodOne comment'),
                ])),
            ],
            'method one of two' => [
                'classDefinition' => new ClassDefinition('ClassName', [
                    new MethodDefinition('methodOne', new Block([
                        new Comment('methodOne comment'),
                    ])),
                    new MethodDefinition('methodTwo', new Block([
                        new Comment('methodTwo comment'),
                    ]))
                ]),
                'name' => 'methodOne',
                'expectedMethod' => new MethodDefinition('methodOne', new Block([
                    new Comment('methodOne comment'),
                ])),
            ],
            'method two of two' => [
                'classDefinition' => new ClassDefinition('ClassName', [
                    new MethodDefinition('methodOne', new Block([
                        new Comment('methodOne comment'),
                    ])),
                    new MethodDefinition('methodTwo', new Block([
                        new Comment('methodTwo comment'),
                    ]))
                ]),
                'name' => 'methodTwo',
                'expectedMethod' => new MethodDefinition('methodTwo', new Block([
                    new Comment('methodTwo comment'),
                ])),
            ],
        ];
    }

    public function testAppendMethod()
    {
        $classDefinition = new ClassDefinition('ClassName', [
            new MethodDefinition('methodOne', new Block([
                new Comment('methodOne comment'),
            ])),
            new MethodDefinition('methodTwo', new Block([
                new Comment('methodTwo comment'),
            ]))
        ]);

        $methodOne = $classDefinition->getMethod('methodOne');

        if ($methodOne instanceof MethodDefinitionInterface) {
            $methodOne->addLine(new Comment('appended'));
        }

        foreach ($classDefinition->getMethods() as $classMethod) {
            if ($classMethod instanceof MethodDefinitionInterface && $classMethod->getName() === 'methodOne') {
                $this->assertEquals(
                    [
                        new Comment('methodOne comment'),
                        new Comment('appended'),
                    ],
                    $classMethod->getLines()
                );
            }
        }
    }
}
