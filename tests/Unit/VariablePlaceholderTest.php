<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\VariablePlaceholder;

class VariablePlaceholderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $name, string $id, string $expectedId)
    {
        $placeholder = new VariablePlaceholder($name, $id);

        $this->assertEquals($expectedId, $placeholder->getId());
    }

    public function createDataProvider(): array
    {
        return [
            'implicit id' => [
                'name' => 'PLACEHOLDER',
                'id' => '',
                'expectedId' => 'PLACEHOLDER',
            ],
            'explicit id' => [
                'name' => 'PLACEHOLDER',
                'id' => 'ID',
                'expectedId' => 'ID',
            ],
        ];
    }

    public function testToString()
    {
        $this->assertSame('{{ NAME }}', (string) new VariablePlaceholder('NAME'));
    }
}
