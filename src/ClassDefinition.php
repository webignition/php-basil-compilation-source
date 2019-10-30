<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class ClassDefinition implements ClassDefinitionInterface
{
    private $name;
    private $functions = [];

    public function __construct(string $name, array $functions)
    {
        $this->name = $name;

        foreach ($functions as $function) {
            if ($function instanceof MethodDefinitionInterface) {
                $this->functions[] = $function;
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
        return $this->functions;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->functions as $function) {
            if ($function instanceof MethodDefinitionInterface) {
                $metadata->add($function->getMetadata());
            }
        }

        return $metadata;
    }

    /**
     * @return SourceInterface[]
     */
    public function getSources(): array
    {
        return $this->getMethods();
    }
}
