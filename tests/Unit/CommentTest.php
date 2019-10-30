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
        $this->assertSame([$comment], $comment->getSources());
    }

    public function testToString()
    {
        $content = 'comment';
        $comment = new Comment($content);

        $this->assertSame($content, $comment->__toString());
    }

    public function testGetType()
    {
        $this->assertSame(Comment::TYPE, (new Comment(''))->getType());
    }

    public function testJsonSerialize()
    {
        $comment = new Comment('comment content');

        $this->assertSame(
            [
                'type' => 'comment',
                'content' => 'comment content',
            ],
            $comment->jsonSerialize()
        );
    }
}
