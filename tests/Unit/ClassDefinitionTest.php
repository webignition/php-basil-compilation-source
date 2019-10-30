<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDefinition;
use webignition\BasilCompilationSource\ClassDefinitionInterface;
use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\EmptyLine;
use webignition\BasilCompilationSource\FunctionDefinition;
use webignition\BasilCompilationSource\Metadata;
use webignition\BasilCompilationSource\MetadataInterface;
use webignition\BasilCompilationSource\Statement;
use webignition\BasilCompilationSource\LineList;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class ClassDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name, array $functions, array $expectedFunctions)
    {
        $classDefinition = new ClassDefinition($name, $functions);

        $this->assertSame($name, $classDefinition->getName());
        $this->assertEquals($classDefinition->getFunctions(), $expectedFunctions);
        $this->assertEquals($classDefinition->getSources(), $expectedFunctions);
    }

    public function constructDataProvider(): array
    {
        return [
            'empty functions' => [
                'name' => 'name1',
                'functions' => [],
                'expectedFunctions' => [],
            ],
            'invalid and valid functions' => [
                'name' => 'name2',
                'functions' => [
                    1,
                    new FunctionDefinition('function1', new LineList()),
                    'string',
                    new FunctionDefinition('function2', new LineList()),
                    true,
                    new FunctionDefinition('function3', new LineList()),
                    new \stdClass(),
                ],
                'expectedFunctions' => [
                    new FunctionDefinition('function1', new LineList()),
                    new FunctionDefinition('function2', new LineList()),
                    new FunctionDefinition('function3', new LineList()),
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
                    new FunctionDefinition('function', new LineList()),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'single function, has lines, no metadata' => [
                'classDefinition' => new ClassDefinition('name', [
                    new FunctionDefinition('function', new LineList([
                        new Statement('statement'),
                    ])),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'single function, has lines, has metadata' => [
                'classDefinition' => new ClassDefinition('name', [
                    new FunctionDefinition('function', new LineList([
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
                    new FunctionDefinition('function', new LineList([
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
                    new FunctionDefinition('function', new LineList([
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

    public function testJsonSerialize()
    {
        $classDefinition = new ClassDefinition(
            'className',
            [
                new FunctionDefinition('functionName', new LineList([
                    new EmptyLine(),
                ])),
            ]
        );

        $this->assertSame(
            [
                'type' => 'class',
                'name' => 'className',
                'functions' => [
                    [
                        'type' => 'function',
                        'name' => 'functionName',
                        'line-list' => [
                            'type' => 'line-list',
                            'lines' => [
                                [
                                    'type' => 'empty',
                                    'content' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $classDefinition->jsonSerialize()
        );
    }
}
