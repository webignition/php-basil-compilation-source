<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\UnknownItemException;
use webignition\BasilCompilationSource\VariablePlaceholder;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class VariablePlaceholderCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $placeholders = [
            new VariablePlaceholder('1'),
            new VariablePlaceholder('2'),
            new VariablePlaceholder('3'),
        ];

        $collection = new VariablePlaceholderCollection($placeholders);

        $this->assertEquals($placeholders, $collection->getAll());
    }

    public function testCreateCollection()
    {
        $names = ['1', '2', '3'];

        $collection = VariablePlaceholderCollection::createCollection($names);

        $this->assertCount(count($names), $collection);

        $this->assertEquals(
            [
                new VariablePlaceholder('1'),
                new VariablePlaceholder('2'),
                new VariablePlaceholder('3'),
            ],
            $collection->getAll()
        );
    }

    public function testCreate()
    {
        $collection = new VariablePlaceholderCollection();

        $this->assertEquals([], $collection->getAll());

        $placeholder = $collection->create('PLACEHOLDER');

        $this->assertInstanceOf(VariablePlaceholder::class, $placeholder);

        $this->assertEquals(
            [
                new VariablePlaceholder('PLACEHOLDER'),
            ],
            $collection->getAll()
        );
    }

    public function testGetItemExists()
    {
        $id = 'PLACEHOLDER';

        $collection = VariablePlaceholderCollection::createCollection([
            $id,
        ]);

        $this->assertEquals(new VariablePlaceholder($id), $collection->get($id));
    }

    public function testGetItemDoesNotExist()
    {
        $this->expectException(UnknownItemException::class);
        $this->expectExceptionMessage('Unknown item "PLACEHOLDER"');

        $collection = new VariablePlaceholderCollection();

        $collection->get('PLACEHOLDER');
    }

    public function testWithAdditionalItems()
    {
        $collection = new VariablePlaceholderCollection();

        $this->assertCount(0, $collection);

        $collection = $collection->withAdditionalItems([
            1,
            true,
            'invalid',
            new VariablePlaceholder('PLACEHOLDER1'),
            new VariablePlaceholder('PLACEHOLDER2'),
        ]);

        $this->assertEquals(
            [
                new VariablePlaceholder('PLACEHOLDER1'),
                new VariablePlaceholder('PLACEHOLDER2'),
            ],
            $collection->getAll()
        );
    }

    public function testMerge()
    {
        $collection1 = VariablePlaceholderCollection::createCollection(['1', '2']);
        $collection2 = VariablePlaceholderCollection::createCollection(['2', '3']);
        $collection3 = VariablePlaceholderCollection::createCollection(['4', '5']);

        $collection = $collection1->merge([
            $collection2,
            $collection3,
        ]);

        $this->assertCount(5, $collection);

        $this->assertEquals(
            [
                new VariablePlaceholder('1'),
                new VariablePlaceholder('2'),
                new VariablePlaceholder('3'),
                new VariablePlaceholder('4'),
                new VariablePlaceholder('5'),
            ],
            $collection->getAll()
        );
    }

    public function testIterator()
    {
        $collectionValues = [
            'ONE' => 'ONE',
            'TWO' => 'TWO',
            'THREE' => 'THREE',
        ];

        $collection = VariablePlaceholderCollection::createCollection(array_values($collectionValues));

        foreach ($collection as $id => $variablePlaceholder) {
            $expectedPlaceholder = new VariablePlaceholder($collectionValues[$id]);

            $this->assertEquals($expectedPlaceholder, $variablePlaceholder);
        }
    }
}
