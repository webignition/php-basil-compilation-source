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
        $this->assertSame([$emptyLine], $emptyLine->getSources());
    }

    public function testToString()
    {
        $this->assertSame('', (string) new EmptyLine());
    }

    public function testGetType()
    {
        $this->assertSame(EmptyLine::TYPE, (new EmptyLine())->getType());
    }

    public function testJsonSerialize()
    {
        $comment = new EmptyLine();

        $this->assertSame(
            [
                'type' => 'empty',
                'content' => '',
            ],
            $comment->jsonSerialize()
        );
    }
}
