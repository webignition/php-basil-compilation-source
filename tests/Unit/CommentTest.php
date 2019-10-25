<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\Comment;

class CommentTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $content = 'comment';
        $comment = new Comment($content);

        $this->assertSame($content, $comment->getContent());
    }

    public function testIsStatement()
    {
        $comment = new Comment('comment');

        $this->assertFalse($comment->isStatement());
    }

    public function testIsComment()
    {
        $comment = new Comment('comment');

        $this->assertTrue($comment->isComment());
    }

    public function testIsEmpty()
    {
        $comment = new Comment('comment');

        $this->assertFalse($comment->isEmpty());
    }

    public function testToString()
    {
        $content = 'comment';
        $comment = new Comment($content);

        $this->assertSame($content, $comment->__toString());
    }
}
