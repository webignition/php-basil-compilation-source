<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line\MethodInvocation;

class ObjectMethodInvocation extends MethodInvocation implements ObjectMethodInvocationInterface
{
    private $object;

    public function __construct(
        string $object,
        string $methodName,
        array $arguments = [],
        int $argumentFormat = ArgumentFormats::INLINE
    ) {
        parent::__construct($methodName, $arguments, $argumentFormat);

        $this->setContent($object . '->' . $this->createString($methodName, $arguments));

        $this->object = $object;
    }

    public function getObject(): string
    {
        return $this->object;
    }
}
