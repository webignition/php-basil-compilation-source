<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\UnknownItemException;

class ClassDependencyCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $classDependencies = [
            new ClassDependency('1'),
            new ClassDependency('2'),
            new ClassDependency('3'),
        ];

        $collection = new ClassDependencyCollection($classDependencies);

        $this->assertEquals($classDependencies, $collection->getAll());
    }

    public function testGetItemExists()
    {
        $className = ClassDependency::class;

        $classDependency = new ClassDependency($className);

        $collection = new ClassDependencyCollection([$classDependency]);

        $this->assertEquals($classDependency, $collection->get($className));
    }

    public function testGetItemDoesNotExist()
    {
        $this->expectException(UnknownItemException::class);
        $this->expectExceptionMessage('Unknown item "class"');

        $collection = new ClassDependencyCollection();

        $collection->get('class');
    }

    public function testWithAdditionalItems()
    {
        $collection = new ClassDependencyCollection();

        $this->assertCount(0, $collection);

        $collection = $collection->withAdditionalItems([
            1,
            true,
            'invalid',
            new ClassDependency('1'),
            new ClassDependency('2'),
        ]);

        $this->assertEquals(
            [
                new ClassDependency('1'),
                new ClassDependency('2'),
            ],
            $collection->getAll()
        );
    }

    public function testMerge()
    {
        $classDependency1 = new ClassDependency('1');
        $classDependency2 = new ClassDependency('2');
        $classDependency3 = new ClassDependency('3');
        $classDependency4 = new ClassDependency('4');
        $classDependency5 = new ClassDependency('5');

        $collection1 = new ClassDependencyCollection([$classDependency1, $classDependency2]);
        $collection2 = new ClassDependencyCollection([$classDependency2, $classDependency3]);
        $collection3 = new ClassDependencyCollection([$classDependency4, $classDependency5]);

        $collection = $collection1->merge([
            $collection2,
            $collection3,
        ]);

        $this->assertCount(5, $collection);

        $this->assertEquals(
            [
                $classDependency1,
                $classDependency2,
                $classDependency3,
                $classDependency4,
                $classDependency5,
            ],
            $collection->getAll()
        );
    }

    public function testIterator()
    {
        $classDependencies = [
            '1' => new ClassDependency('1'),
            '2' => new ClassDependency('2'),
            '3' => new ClassDependency('3'),
        ];

        $collection = new ClassDependencyCollection(array_values($classDependencies));

        foreach ($collection as $id => $classDependency) {
            $expectedClassDependency = new ClassDependency($id);

            $this->assertEquals($expectedClassDependency, $classDependency);
        }
    }

    public function testAddLine()
    {
        $classDependency1 = new ClassDependency('1');
        $classDependency2 = new ClassDependency('2');

        $collection = new ClassDependencyCollection([$classDependency1]);
        $this->assertEquals([$classDependency1], $collection->getLines());

        $collection->addLine($classDependency2);

        $expectedSources = [$classDependency1, $classDependency2];

        $this->assertEquals($expectedSources, $collection->getLines());
        $this->assertEquals($expectedSources, $collection->getAll());
        $this->assertEquals($expectedSources, $collection->getSources());
    }

    public function testAddLinesFromSource()
    {
        $classDependency1 = new ClassDependency('1');
        $classDependency2 = new ClassDependency('2');

        $collection = new ClassDependencyCollection([$classDependency1]);
        $this->assertEquals([$classDependency1], $collection->getLines());

        $collection->addLinesFromSource($classDependency2);

        $expectedSources = [$classDependency1, $classDependency2];

        $this->assertEquals($expectedSources, $collection->getLines());
        $this->assertEquals($expectedSources, $collection->getAll());
        $this->assertEquals($expectedSources, $collection->getSources());
    }

    public function testAddLinesFromSources()
    {
        $classDependency1 = new ClassDependency('1');
        $classDependency2 = new ClassDependency('2');

        $collection = new ClassDependencyCollection([$classDependency1]);
        $this->assertEquals([$classDependency1], $collection->getLines());

        $collection->addLinesFromSources([$classDependency2]);

        $expectedSources = [$classDependency1, $classDependency2];

        $this->assertEquals($expectedSources, $collection->getLines());
        $this->assertEquals($expectedSources, $collection->getAll());
        $this->assertEquals($expectedSources, $collection->getSources());
    }

    public function testGetMetadata()
    {
        $this->assertEquals(new Metadata(), (new ClassDependencyCollection())->getMetadata());
    }
}
