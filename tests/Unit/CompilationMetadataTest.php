<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\CompilationMetadata;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class CompilationMetadataTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $compilationMetadata = new CompilationMetadata();

        $this->assertEquals(new ClassDependencyCollection(), $compilationMetadata->getClassDependencies());
        $this->assertEquals(new VariablePlaceholderCollection(), $compilationMetadata->getVariableDependencies());
        $this->assertEquals(new VariablePlaceholderCollection(), $compilationMetadata->getVariableExports());
    }

    public function testWithClassDependencies()
    {
        $emptyClassDependencies = new ClassDependencyCollection();
        $classDependencies = new ClassDependencyCollection([
            new ClassDependency('1'),
        ]);

        $compilationMetadata = new CompilationMetadata();
        $this->assertEquals($emptyClassDependencies, $compilationMetadata->getClassDependencies());

        $compilationMetadata = $compilationMetadata->withClassDependencies($classDependencies);
        $this->assertEquals($classDependencies, $compilationMetadata->getClassDependencies());

        $compilationMetadata = $compilationMetadata->withClassDependencies($emptyClassDependencies);
        $this->assertEquals($emptyClassDependencies, $compilationMetadata->getClassDependencies());
    }

    public function testWithVariableDependencies()
    {
        $emptyVariableDependencies = new VariablePlaceholderCollection();
        $variableDependencies = VariablePlaceholderCollection::createCollection(['1']);

        $compilationMetadata = new CompilationMetadata();
        $this->assertEquals($emptyVariableDependencies, $compilationMetadata->getVariableDependencies());

        $compilationMetadata = $compilationMetadata->withVariableDependencies($variableDependencies);
        $this->assertEquals($variableDependencies, $compilationMetadata->getVariableDependencies());

        $compilationMetadata = $compilationMetadata->withVariableDependencies($emptyVariableDependencies);
        $this->assertEquals($emptyVariableDependencies, $compilationMetadata->getVariableDependencies());
    }

    public function testWithVariableExports()
    {
        $emptyVariableExports = new VariablePlaceholderCollection();
        $variableExports = VariablePlaceholderCollection::createCollection(['1']);

        $compilationMetadata = new CompilationMetadata();
        $this->assertEquals($emptyVariableExports, $compilationMetadata->getVariableExports());

        $compilationMetadata = $compilationMetadata->withVariableExports($variableExports);
        $this->assertEquals($variableExports, $compilationMetadata->getVariableExports());

        $compilationMetadata = $compilationMetadata->withVariableExports($emptyVariableExports);
        $this->assertEquals($emptyVariableExports, $compilationMetadata->getVariableExports());
    }

    public function testWithAdditionalClassDependencies()
    {
        $classDependencies1 = new ClassDependencyCollection([
            new ClassDependency('1'),
        ]);

        $classDependencies2 = new ClassDependencyCollection([
            new ClassDependency('2'),
        ]);

        $compilationMetadata = new CompilationMetadata();
        $this->assertEquals(new ClassDependencyCollection(), $compilationMetadata->getClassDependencies());

        $compilationMetadata = $compilationMetadata->withAdditionalClassDependencies($classDependencies1);
        $this->assertEquals(
            new ClassDependencyCollection([
                new ClassDependency('1'),
            ]),
            $compilationMetadata->getClassDependencies()
        );

        $compilationMetadata = $compilationMetadata->withAdditionalClassDependencies($classDependencies2);
        $this->assertEquals(
            new ClassDependencyCollection([
                new ClassDependency('1'),
                new ClassDependency('2'),
            ]),
            $compilationMetadata->getClassDependencies()
        );
    }

    public function testWithAdditionalVariableDependencies()
    {
        $variableDependencies1 = VariablePlaceholderCollection::createCollection(['1']);
        $variableDependencies2 = VariablePlaceholderCollection::createCollection(['2']);

        $compilationMetadata = new CompilationMetadata();
        $this->assertEquals(new VariablePlaceholderCollection(), $compilationMetadata->getVariableDependencies());

        $compilationMetadata = $compilationMetadata->withAdditionalVariableDependencies($variableDependencies1);
        $this->assertEquals(
            VariablePlaceholderCollection::createCollection(['1']),
            $compilationMetadata->getVariableDependencies()
        );

        $compilationMetadata = $compilationMetadata->withAdditionalVariableDependencies($variableDependencies2);
        $this->assertEquals(
            VariablePlaceholderCollection::createCollection(['1', '2']),
            $compilationMetadata->getVariableDependencies()
        );
    }

    public function testWithAdditionalVariableExports()
    {
        $variableExports1 = VariablePlaceholderCollection::createCollection(['1']);
        $variableExports2 = VariablePlaceholderCollection::createCollection(['2']);

        $compilationMetadata = new CompilationMetadata();
        $this->assertEquals(new VariablePlaceholderCollection(), $compilationMetadata->getVariableExports());

        $compilationMetadata = $compilationMetadata->withAdditionalVariableExports($variableExports1);
        $this->assertEquals(
            VariablePlaceholderCollection::createCollection(['1']),
            $compilationMetadata->getVariableExports()
        );

        $compilationMetadata = $compilationMetadata->withAdditionalVariableExports($variableExports2);
        $this->assertEquals(
            VariablePlaceholderCollection::createCollection(['1', '2']),
            $compilationMetadata->getVariableExports()
        );
    }

    public function testMerge()
    {
        $compilationMetadata = new CompilationMetadata();
        $compilationMetadata1 = (new CompilationMetadata())
            ->withClassDependencies(new ClassDependencyCollection([
                new ClassDependency('class1'),
            ]));

        $compilationMetadata2 = (new CompilationMetadata())
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1']));

        $compilationMetadata3 = (new CompilationMetadata())
            ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport1']));

        $compilationMetadata4 = (new CompilationMetadata())
            ->withClassDependencies(new ClassDependencyCollection([
                new ClassDependency('class2'),
            ]))
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency2']))
            ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport2']));

        $compilationMetadata = $compilationMetadata->merge([$compilationMetadata1]);
        $this->assertEquals(
            (new CompilationMetadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                ])),
            $compilationMetadata
        );

        $compilationMetadata = $compilationMetadata->merge([$compilationMetadata2]);
        $this->assertEquals(
            (new CompilationMetadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                ]))
                ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1'])),
            $compilationMetadata
        );

        $compilationMetadata = $compilationMetadata->merge([$compilationMetadata3]);
        $this->assertEquals(
            (new CompilationMetadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                ]))
                ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1']))
                ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport1'])),
            $compilationMetadata
        );

        $compilationMetadata = $compilationMetadata->merge([$compilationMetadata4]);
        $this->assertEquals(
            (new CompilationMetadata())
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
            $compilationMetadata
        );
    }
}
