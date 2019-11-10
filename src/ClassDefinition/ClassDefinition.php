<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\ClassDefinition;

use webignition\BasilCompilationSource\Metadata\Metadata;
use webignition\BasilCompilationSource\Metadata\MetadataInterface;
use webignition\BasilCompilationSource\MethodDefinition\MethodDefinitionInterface;

class ClassDefinition implements ClassDefinitionInterface
{
    private $name;

    /**
     * @var MethodDefinitionInterface[]
     */
    private $methods = [];

    public function __construct(string $name, array $methods)
    {
        $this->name = $name;

        foreach ($methods as $method) {
            if ($method instanceof MethodDefinitionInterface) {
                $this->methods[$method->getName()] = $method;
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return MethodDefinitionInterface[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->methods as $function) {
            if ($function instanceof MethodDefinitionInterface) {
                $metadata->add($function->getMetadata());
            }
        }

        return $metadata;
    }

    public function getMethod(string $name): ?MethodDefinitionInterface
    {
        return $this->methods[$name] ?? null;
    }
}
