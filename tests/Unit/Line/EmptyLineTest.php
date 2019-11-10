<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Line;

use webignition\BasilCompilationSource\Line\EmptyLine;
use webignition\BasilCompilationSource\Line\LineTypes;

class EmptyLineTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $emptyLine = new EmptyLine();

        $this->assertSame('', $emptyLine->getContent());
    }

    public function testToString()
    {
        $this->assertSame('', (string) new EmptyLine());
    }

    public function testGetType()
    {
        $this->assertSame(LineTypes::EMPTY, (new EmptyLine())->getType());
    }
}
