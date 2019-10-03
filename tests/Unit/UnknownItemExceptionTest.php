<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\UnknownItemException;

class UnknownItemExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetId()
    {
        $unknownItemException = new UnknownItemException('1');

        $this->assertSame('1', $unknownItemException->getId());
    }
}
