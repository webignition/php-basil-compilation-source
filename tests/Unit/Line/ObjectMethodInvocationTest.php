<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Line;

use webignition\BasilCompilationSource\Line\LineTypes;
use webignition\BasilCompilationSource\Line\MethodInvocation\ArgumentFormats;
use webignition\BasilCompilationSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilationSource\Line\MethodInvocation\ObjectMethodInvocation;

class ObjectMethodInvocationTest extends \PHPUnit\Framework\TestCase
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
        string $object,
        string $methodName,
        array $arguments,
        int $argumentFormat,
        string $expectedStringRepresentation
    ) {
        $invocation = new ObjectMethodInvocation($object, $methodName, $arguments, $argumentFormat);

        $this->assertSame($object, $invocation->getObject());
        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame($argumentFormat, $invocation->getArgumentFormat());
        $this->assertSame(LineTypes::METHOD_INVOCATION, $invocation->getType());
        $this->assertSame($expectedStringRepresentation, $invocation->getContent());
        $this->assertSame($expectedStringRepresentation, $invocation->__toString());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'objectName' => 'object',
                'methodName' => 'method',
                'arguments' => [],
                'argumentFormat' => ArgumentFormats::INLINE,
                'expectedStringRepresentation' => 'object->method()'
            ],
            'single argument' => [
                'objectName' => 'object',
                'methodName' => 'method',
                'arguments' => [
                    1,
                ],
                'argumentFormat' => ArgumentFormats::INLINE,
                'expectedStringRepresentation' => 'object->method(1)'
            ],
            'multiple arguments, inline' => [
                'objectName' => 'object',
                'methodName' => 'method',
                'arguments' => [
                    2,
                    "'single-quoted value'",
                    '"double-quoted value"'
                ],
                'argumentFormat' => ArgumentFormats::INLINE,
                'expectedStringRepresentation' => 'object->method(2, \'single-quoted value\', "double-quoted value")'
            ],
            'multiple arguments, stacked' => [
                'objectName' => 'object',
                'methodName' => 'method',
                'arguments' => [
                    2,
                    "'single-quoted value'",
                    '"double-quoted value"'
                ],
                'argumentFormat' => ArgumentFormats::STACKED,
                'expectedStringRepresentation' => 'object->method(2, \'single-quoted value\', "double-quoted value")'
            ],
        ];
    }
}
