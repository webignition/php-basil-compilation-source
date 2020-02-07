<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Line;

use webignition\BasilCompilationSource\Line\LineTypes;
use webignition\BasilCompilationSource\Line\MethodInvocation\ArgumentFormats;
use webignition\BasilCompilationSource\Line\MethodInvocation\MethodInvocation;

class MethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $methodName
     * @param string[] $arguments
     * @param int $argumentFormat
     * @param string $expectedStringRepresentation
     */
    public function testCreate(
        string $methodName,
        array $arguments,
        int $argumentFormat,
        string $expectedStringRepresentation
    ) {
        $methodInvocation = new MethodInvocation($methodName, $arguments, $argumentFormat);

        $this->assertSame($methodName, $methodInvocation->getMethodName());
        $this->assertSame($arguments, $methodInvocation->getArguments());
        $this->assertSame($argumentFormat, $methodInvocation->getArgumentFormat());
        $this->assertSame(LineTypes::METHOD_INVOCATION, $methodInvocation->getType());
        $this->assertSame($expectedStringRepresentation, $methodInvocation->getContent());
        $this->assertSame($expectedStringRepresentation, $methodInvocation->__toString());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'methodName' => 'method',
                'arguments' => [],
                'argumentFormat' => ArgumentFormats::INLINE,
                'expectedStringRepresentation' => 'method()'
            ],
            'single argument' => [
                'methodName' => 'method',
                'arguments' => [
                    1,
                ],
                'argumentFormat' => ArgumentFormats::INLINE,
                'expectedStringRepresentation' => 'method(1)'
            ],
            'multiple arguments, inline' => [
                'methodName' => 'method',
                'arguments' => [
                    2,
                    "'single-quoted value'",
                    '"double-quoted value"'
                ],
                'argumentFormat' => ArgumentFormats::INLINE,
                'expectedStringRepresentation' => 'method(2, \'single-quoted value\', "double-quoted value")'
            ],
            'multiple arguments, stacked' => [
                'methodName' => 'method',
                'arguments' => [
                    2,
                    "'single-quoted value'",
                    '"double-quoted value"'
                ],
                'argumentFormat' => ArgumentFormats::STACKED,
                'expectedStringRepresentation' => 'method(2, \'single-quoted value\', "double-quoted value")'
            ],
        ];
    }
}
