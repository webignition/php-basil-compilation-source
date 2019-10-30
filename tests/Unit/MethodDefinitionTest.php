<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\MethodDefinition;
use webignition\BasilCompilationSource\LineList;

class MethodDefinitionTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $visibility = MethodDefinition::VISIBILITY_PUBLIC;

        $methodDefinition = new MethodDefinition($visibility, 'name', new LineList());

        $this->assertSame($visibility, $methodDefinition->getVisibility());
    }
}
