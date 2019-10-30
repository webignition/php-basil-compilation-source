<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\LineTypes;
use webignition\BasilCompilationSource\Metadata;

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
     * @dataProvider contentDataProvider
     */
    public function testGetContent(ClassDependency $classDependency, string $expectedString)
    {
        $this->assertSame($expectedString, $classDependency->getContent());
    }

    /**
     * @dataProvider contentDataProvider
     */
    public function testGetId(ClassDependency $classDependency, string $expectedId)
    {
        $this->assertSame($expectedId, $classDependency->getId());
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

    public function testGetMetadata()
    {
        $this->assertEquals(new Metadata(), (new ClassDependency(ClassDependency::class))->getMetadata());
    }

    public function testGetSources()
    {
        $classDependency = new ClassDependency(ClassDependency::class);

        $this->assertSame([$classDependency], $classDependency->getSources());
    }

    public function testGetType()
    {
        $this->assertSame(
            LineTypes::USE_STATEMENT,
            (new ClassDependency(ClassDependency::class))->getType()
        );
    }
}
