<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\EmptyLine;

class EmptyLineTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $emptyLine = new EmptyLine();

        $this->assertSame('', $emptyLine->getContent());
    }

    public function testIsStatement()
    {
        $emptyLine = new EmptyLine();

        $this->assertFalse($emptyLine->isStatement());
    }

    public function testIsComment()
    {
        $emptyLine = new EmptyLine();

        $this->assertFalse($emptyLine->isComment());
    }

    public function testIsEmpty()
    {
        $emptyLine = new EmptyLine();

        $this->assertTrue($emptyLine->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('', (string) new EmptyLine());
    }

    public function testGetType()
    {
        $this->assertSame(EmptyLine::TYPE, (new EmptyLine())->getType());
    }
}
