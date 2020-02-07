<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line\MethodInvocation;

use webignition\BasilCompilationSource\Line\AbstractLine;
use webignition\BasilCompilationSource\Line\LineTypes;
use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;

class MethodInvocation extends AbstractLine implements MethodInvocationInterface
{
    private const STRING_PATTERN = '%s(%s)';

    private $methodName;
    private $arguments = [];
    private $argumentFormat;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @param string $methodName
     * @param string[] $arguments
     * @param int $argumentFormat
     */
    public function __construct(
        string $methodName,
        array $arguments = [],
        int $argumentFormat = ArgumentFormats::INLINE
    ) {
        parent::__construct($this->createString($methodName, $arguments), LineTypes::METHOD_INVOCATION);

        $this->methodName = $methodName;
        $this->arguments = $arguments;
        $this->argumentFormat = $argumentFormat;
        $this->metadata = new Metadata();
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgumentFormat(): int
    {
        return $this->argumentFormat;
    }

    public function withMetadata(MetadataInterface $metadata): MethodInvocationInterface
    {
        $new = clone $this;
        $new->metadata = $metadata;

        return $new;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @param string $methodName
     * @param string[] $arguments
     *
     * @return string
     */
    protected function createString(string $methodName, array $arguments): string
    {
        return sprintf(
            self::STRING_PATTERN,
            $methodName,
            implode(', ', $arguments)
        );
    }
}
