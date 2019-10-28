<?php declare(strict_types=1);

namespace webignition\BasilCompilationSource;

interface LineInterface extends \JsonSerializable
{
    public function getContent(): string;
    public function isStatement(): bool;
    public function isComment(): bool;
    public function isEmpty(): bool;
    public function getType(): string;
    public function __toString(): string;
}
