<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Metadata;

use webignition\BasilCompilationSource\Block\ClassDependencyCollection;
use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class MetadataTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $metadata = new Metadata();

        $this->assertEquals(new ClassDependencyCollection(), $metadata->getClassDependencies());
        $this->assertEquals(new VariablePlaceholderCollection(), $metadata->getVariableDependencies());
        $this->assertEquals(new VariablePlaceholderCollection(), $metadata->getVariableExports());
    }

    public function testWithClassDependencies()
    {
        $emptyClassDependencies = new ClassDependencyCollection();
        $classDependencies = new ClassDependencyCollection([
            new ClassDependency('1'),
        ]);

        $metadata = new Metadata();
        $this->assertEquals($emptyClassDependencies, $metadata->getClassDependencies());

        $metadata = $metadata->withClassDependencies($classDependencies);
        $this->assertEquals($classDependencies, $metadata->getClassDependencies());

        $metadata = $metadata->withClassDependencies($emptyClassDependencies);
        $this->assertEquals($emptyClassDependencies, $metadata->getClassDependencies());
    }

    public function testWithVariableDependencies()
    {
        $emptyVariableDependencies = new VariablePlaceholderCollection();
        $variableDependencies = VariablePlaceholderCollection::createCollection(['1']);

        $metadata = new Metadata();
        $this->assertEquals($emptyVariableDependencies, $metadata->getVariableDependencies());

        $metadata = $metadata->withVariableDependencies($variableDependencies);
        $this->assertEquals($variableDependencies, $metadata->getVariableDependencies());

        $metadata = $metadata->withVariableDependencies($emptyVariableDependencies);
        $this->assertEquals($emptyVariableDependencies, $metadata->getVariableDependencies());
    }

    public function testWithVariableExports()
    {
        $emptyVariableExports = new VariablePlaceholderCollection();
        $variableExports = VariablePlaceholderCollection::createCollection(['1']);

        $metadata = new Metadata();
        $this->assertEquals($emptyVariableExports, $metadata->getVariableExports());

        $metadata = $metadata->withVariableExports($variableExports);
        $this->assertEquals($variableExports, $metadata->getVariableExports());

        $metadata = $metadata->withVariableExports($emptyVariableExports);
        $this->assertEquals($emptyVariableExports, $metadata->getVariableExports());
    }

    public function testAddClassDependencies()
    {
        $classDependencies1 = new ClassDependencyCollection([
            new ClassDependency('1'),
        ]);

        $classDependencies2 = new ClassDependencyCollection([
            new ClassDependency('2'),
        ]);

        $metadata = new Metadata();
        $this->assertEquals(new ClassDependencyCollection(), $metadata->getClassDependencies());

        $metadata->addClassDependencies($classDependencies1);
        $this->assertEquals(
            new ClassDependencyCollection([
                new ClassDependency('1'),
            ]),
            $metadata->getClassDependencies()
        );

        $metadata->addClassDependencies($classDependencies2);
        $this->assertEquals(
            new ClassDependencyCollection([
                new ClassDependency('1'),
                new ClassDependency('2'),
            ]),
            $metadata->getClassDependencies()
        );
    }

    public function testAddVariableDependencies()
    {
        $variableDependencies1 = VariablePlaceholderCollection::createCollection(['1']);
        $variableDependencies2 = VariablePlaceholderCollection::createCollection(['2']);

        $metadata = new Metadata();
        $this->assertEquals(new VariablePlaceholderCollection(), $metadata->getVariableDependencies());

        $metadata->addVariableDependencies($variableDependencies1);
        $this->assertEquals(
            VariablePlaceholderCollection::createCollection(['1']),
            $metadata->getVariableDependencies()
        );

        $metadata->addVariableDependencies($variableDependencies2);
        $this->assertEquals(
            VariablePlaceholderCollection::createCollection(['1', '2']),
            $metadata->getVariableDependencies()
        );
    }

    public function testAddlVariableExports()
    {
        $variableExports1 = VariablePlaceholderCollection::createCollection(['1']);
        $variableExports2 = VariablePlaceholderCollection::createCollection(['2']);

        $metadata = new Metadata();
        $this->assertEquals(new VariablePlaceholderCollection(), $metadata->getVariableExports());

        $metadata->addVariableExports($variableExports1);
        $this->assertEquals(
            VariablePlaceholderCollection::createCollection(['1']),
            $metadata->getVariableExports()
        );

        $metadata->addVariableExports($variableExports2);
        $this->assertEquals(
            VariablePlaceholderCollection::createCollection(['1', '2']),
            $metadata->getVariableExports()
        );
    }

    public function testAdd()
    {
        $metadata = new Metadata();
        $compilationMetadata1 = (new Metadata())
            ->withClassDependencies(new ClassDependencyCollection([
                new ClassDependency('class1'),
            ]));

        $compilationMetadata2 = (new Metadata())
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1']));

        $compilationMetadata3 = (new Metadata())
            ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport1']));

        $compilationMetadata4 = (new Metadata())
            ->withClassDependencies(new ClassDependencyCollection([
                new ClassDependency('class2'),
            ]))
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency2']))
            ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport2']));

        $metadata->add($compilationMetadata1);
        $this->assertEquals(
            (new Metadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                ])),
            $metadata
        );

        $metadata->add($compilationMetadata2);
        $this->assertEquals(
            (new Metadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                ]))
                ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1'])),
            $metadata
        );

        $metadata->add($compilationMetadata3);
        $this->assertEquals(
            (new Metadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                ]))
                ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1']))
                ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport1'])),
            $metadata
        );

        $metadata->add($compilationMetadata4);
        $this->assertEquals(
            (new Metadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                    new ClassDependency('class2'),
                ]))
                ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                    'variableDependency1',
                    'variableDependency2',
                ]))
                ->withVariableExports(VariablePlaceholderCollection::createCollection([
                    'variableExport1',
                    'variableExport2',
                ])),
            $metadata
        );
    }
}
