<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\VariablePlaceholder;

class VariablePlaceholderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $name)
    {
        $placeholder = new VariablePlaceholder($name);

        $this->assertEquals($name, $placeholder->getName());
    }

    public function createDataProvider(): array
    {
        return [
            'default' => [
                'name' => 'PLACEHOLDER',
            ],
        ];
    }

    public function testToString()
    {
        $this->assertSame('{{ NAME }}', (string) new VariablePlaceholder('NAME'));
    }
}
