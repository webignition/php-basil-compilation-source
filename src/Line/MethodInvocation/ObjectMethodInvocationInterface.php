<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line\MethodInvocation;

interface ObjectMethodInvocationInterface extends MethodInvocationInterface
{
    public function getObject(): string;
}
