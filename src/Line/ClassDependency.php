<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Line;

use webignition\BasilCompilationSource\UniqueItemInterface;

class ClassDependency extends AbstractStatement implements UniqueItemInterface
{
    private $className;
    private $alias;

    public function __construct(string $className, ?string $alias = null)
    {
        parent::__construct($this->createContent($className, $alias), LineTypes::USE_STATEMENT);

        $this->className = $className;
        $this->alias = $alias;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getId(): string
    {
        return $this->content;
    }

    private function createContent(string $className, ?string $alias = null)
    {
        $content = $className;

        if (null !== $alias) {
            $content .= ' as ' . $alias;
        }

        return $content;
    }
}