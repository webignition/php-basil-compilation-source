<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Line;

use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Line\LineTypes;

class ClassDependencyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name, ?string $alias, string $expectedContent)
    {
        $classDependency = new ClassDependency($name, $alias);

        $this->assertSame($expectedContent, $classDependency->getContent());
    }

    public function constructDataProvider(): array
    {
        return [
            'name, no alias' => [
                'name' => ClassDependency::class,
                'alias' => null,
                'expectedContent' => ClassDependency::class,
            ],
            'name, has alias' => [
                'name' => ClassDependency::class,
                'alias' => 'CD',
                'expectedContent' => ClassDependency::class . ' as CD',
            ],
        ];
    }

    /**
     * @dataProvider contentDataProvider
     */
    public function testGetContent(ClassDependency $classDependency, string $expectedString)
    {
        $this->assertSame($expectedString, $classDependency->getContent());
    }

    /**
     * @dataProvider contentDataProvider
     */
    public function testToString(ClassDependency $classDependency, string $expectedString)
    {
        $this->assertSame($expectedString, (string) $classDependency);
    }

    public function contentDataProvider(): array
    {
        return [
            'no alias' => [
                'classDependency' => new ClassDependency(
                    ClassDependency::class
                ),
                'expectedId' => ClassDependency::class,
            ],
            'has alias' => [
                'classDependency' => new ClassDependency(
                    ClassDependency::class,
                    'CD'
                ),
                'expectedId' => ClassDependency::class . ' as CD',
            ],
        ];
    }

    public function testGetType()
    {
        $this->assertSame(
            LineTypes::USE_STATEMENT,
            (new ClassDependency(ClassDependency::class))->getType()
        );
    }
}
