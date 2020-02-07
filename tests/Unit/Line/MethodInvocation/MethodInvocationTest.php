<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit\Line\MethodInvocation;

use webignition\BasilCompilationSource\Line\LineTypes;
use webignition\BasilCompilationSource\Line\MethodInvocation\ArgumentFormats;
use webignition\BasilCompilationSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

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
        $invocation = new MethodInvocation($methodName, $arguments, $argumentFormat);

        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame($argumentFormat, $invocation->getArgumentFormat());
        $this->assertSame(LineTypes::METHOD_INVOCATION, $invocation->getType());
        $this->assertSame($expectedStringRepresentation, $invocation->getContent());
        $this->assertSame($expectedStringRepresentation, $invocation->__toString());
        $this->assertEquals(new Metadata(), $invocation->getMetadata());
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

    public function testWithMetadata()
    {
        $metadata = (new Metadata())
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection([
                'variableDependencyOne',
                'variableDependencyTwo',
            ]));

        $invocation = new MethodInvocation('methodName');
        $this->assertEquals(new Metadata(), $invocation->getMetadata());

        $mutatedInvocation = $invocation->withMetadata($metadata);
        $this->assertSame($metadata, $mutatedInvocation->getMetadata());
        $this->assertNotEquals($metadata, $invocation->getMetadata());
    }
}
