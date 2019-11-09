<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\VariablePlaceholder;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class VariablePlaceholderCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $placeholders = [
            'ONE' => new VariablePlaceholder('ONE'),
            'TWO' => new VariablePlaceholder('TWO'),
            'THREE' => new VariablePlaceholder('THREE'),
        ];

        $collection = new VariablePlaceholderCollection($placeholders);

        $this->assertEquals($placeholders, $this->getCollectionVariablePlaceholders($collection));
    }

    public function testCreateCollection()
    {
        $names = ['ONE', 'TWO', 'TWO', 'THREE'];

        $collection = VariablePlaceholderCollection::createCollection($names);

        $expectedPlaceholders = [
            'ONE' => new VariablePlaceholder('ONE'),
            'TWO' => new VariablePlaceholder('TWO'),
            'THREE' => new VariablePlaceholder('THREE'),
        ];

        $this->assertCount(count($expectedPlaceholders), $collection);

        $this->assertEquals($expectedPlaceholders, $this->getCollectionVariablePlaceholders($collection));
    }

    public function testCreate()
    {
        $collection = new VariablePlaceholderCollection();
        $this->assertEquals([], $this->getCollectionVariablePlaceholders($collection));

        $placeholder = $collection->create('PLACEHOLDER');

        $this->assertInstanceOf(VariablePlaceholder::class, $placeholder);
        $this->assertEquals(
            [
                'PLACEHOLDER' => new VariablePlaceholder('PLACEHOLDER'),
            ],
            $this->getCollectionVariablePlaceholders($collection)
        );
    }

    public function testMerge()
    {
        $collection = VariablePlaceholderCollection::createCollection(['ONE']);

        $collection->merge([
            VariablePlaceholderCollection::createCollection(['TWO', 'THREE']),
            VariablePlaceholderCollection::createCollection(['THREE', 'FOUR']),
        ]);

        $this->assertCount(4, $collection);

        $this->assertEquals(
            [
                'ONE' => new VariablePlaceholder('ONE'),
                'TWO' => new VariablePlaceholder('TWO'),
                'THREE' => new VariablePlaceholder('THREE'),
                'FOUR' => new VariablePlaceholder('FOUR'),
            ],
            $this->getCollectionVariablePlaceholders($collection)
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

    /**
     * @param VariablePlaceholderCollection $collection
     *
     * @return VariablePlaceholder[]
     */
    private function getCollectionVariablePlaceholders(VariablePlaceholderCollection $collection): array
    {
        $reflectionObject = new \ReflectionObject($collection);
        $property = $reflectionObject->getProperty('variablePlaceholders');
        $property->setAccessible(true);

        return $property->getValue($collection);
    }
}
