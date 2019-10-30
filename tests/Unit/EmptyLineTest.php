<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\EmptyLine;
use webignition\BasilCompilationSource\LineTypes;

class EmptyLineTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $emptyLine = new EmptyLine();

        $this->assertSame('', $emptyLine->getContent());
        $this->assertSame([$emptyLine], $emptyLine->getSources());
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
