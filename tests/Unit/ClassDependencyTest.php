<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;

class ClassDependencyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $name, ?string $alias)
    {
        $classDependency = new ClassDependency($name, $alias);

        $this->assertSame($name, $classDependency->getClassName());
        $this->assertSame($alias, $classDependency->getAlias());
    }

    public function createDataProvider(): array
    {
        return [
            'name, no alias' => [
                'name' => ClassDependency::class,
                'alias' => '',
            ],
            'name, has alias' => [
                'name' => ClassDependency::class,
                'alias' => 'CD',
            ],
        ];
    }

    /**
     * @dataProvider getIdDataProvider
     */
    public function testGetId(ClassDependency $classDependency, string $expectedId)
    {
        $this->assertSame($expectedId, $classDependency->getId());
    }

    public function getIdDataProvider(): array
    {
        return [
            'name, no alias' => [
                'classDependency' => new ClassDependency(
                    ClassDependency::class
                ),
                'expectedId' => ClassDependency::class,
            ],
            'name, has alias' => [
                'classDependency' => new ClassDependency(
                    ClassDependency::class,
                    'CD'
                ),
                'expectedId' => ClassDependency::class . ':CD',
            ],
        ];
    }
}
