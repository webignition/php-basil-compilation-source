<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Line;

use webignition\BasilCompilationSource\Line\Comment;
use webignition\BasilCompilationSource\Line\LineTypes;

class CommentTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $content = 'comment';
        $comment = new Comment($content);

        $this->assertSame($content, $comment->getContent());
    }

    public function testToString()
    {
        $content = 'comment';
        $comment = new Comment($content);

        $this->assertSame($content, $comment->__toString());
    }

    public function testGetType()
    {
        $this->assertSame(LineTypes::COMMENT, (new Comment(''))->getType());
    }
}
