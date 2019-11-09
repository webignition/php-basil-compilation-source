<?php

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Block;

use webignition\BasilCompilationSource\Line\ClassDependency;
use webignition\BasilCompilationSource\Line\LineInterface;

class ClassDependencyCollection extends AbstractBlock
{
    protected function canLineBeAdded(LineInterface $line): bool
    {
        if ($line instanceof ClassDependency) {
            return false === $this->containsClassDependency($line);
        }

        return false;
    }

    private function containsClassDependency(ClassDependency $classDependency): bool
    {
        foreach ($this->lines as $line) {
            if ($line->getContent() === $classDependency->getContent()) {
                return true;
            }
        }

        return false;
    }
}
