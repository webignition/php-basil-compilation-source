<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

class MethodDefinition extends FunctionDefinition implements MethodDefinitionInterface
{
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PROTECTED = 'protected';
    const VISIBILITY_PRIVATE = 'private';

    private $visibility;

    public function __construct(string $visibility, string $name, LineList $lineList, ?array $arguments = null)
    {
        parent::__construct($name, $lineList, $arguments);

        $this->visibility = $visibility;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
