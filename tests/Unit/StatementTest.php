<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\MetadataInterface;
use webignition\BasilCompilationSource\Statement;
use webignition\BasilCompilationSource\StatementList;
use webignition\BasilCompilationSource\StatementListInterface;
use webignition\BasilCompilationSource\Metadata;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class StatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $content, $metadata, MetadataInterface $expectedMetadata)
    {
        $statement = new Statement($content, $metadata);

        $this->assertSame($content, $statement->getContent());
        $this->assertEquals($expectedMetadata, $statement->getMetadata());
    }

    public function constructDataProvider(): array
    {
        $metadata = (new Metadata())->withVariableDependencies(VariablePlaceholderCollection::createCollection([
            'PLACEHOLDER',
        ]));

        return [
            'without metadata' => [
                'content' => 'statement',
                'metadata' => null,
                'expectedMetadata' => new Metadata(),
            ],
            'with metadata' => [
                'content' => 'statement',
                'metadata' => $metadata,
                'expectedMetadata' => $metadata,
            ],
        ];
    }

    public function testGetStatements()
    {
        $content = 'statement';
        $expectedStatements = [$content];
        $statement = new Statement($content);

        $this->assertSame($expectedStatements, $statement->getStatements());
    }

    public function testToString()
    {
        $content = 'statement';
        $statement = new Statement($content);

        $this->assertSame($content, $statement->__toString());
    }
}
