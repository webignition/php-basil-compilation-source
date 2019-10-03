<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\BasilCompilationSource\Tests\Unit;

use webignition\BasilCompilationSource\ClassDependency;
use webignition\BasilCompilationSource\ClassDependencyCollection;
use webignition\BasilCompilationSource\CompilableSource;
use webignition\BasilCompilationSource\CompilationMetadata;
use webignition\BasilCompilationSource\CompilationMetadataInterface;
use webignition\BasilCompilationSource\VariablePlaceholderCollection;

class CompilableSourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(
        array $statements,
        ?CompilationMetadataInterface $compilationMetadata,
        CompilationMetadataInterface $expectedCompilationMetadata
    ) {
        $compilableSource = new CompilableSource($statements, $compilationMetadata);

        $this->assertSame($statements, $compilableSource->getStatements());
        $this->assertEquals($expectedCompilationMetadata, $compilableSource->getCompilationMetadata());
    }

    public function constructDataProvider(): array
    {
        return [
            'statements only' => [
                'statements' => [
                    'statement1',
                    'statement2',
                ],
                'compilationMetadata' => null,
                'expectedCompilationMetadata' => new CompilationMetadata(),
            ],
            'statements and compilation metadata' => [
                'statements' => [
                    'statement1',
                    'statement2',
                ],
                'compilationMetadata' => (new CompilationMetadata())
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1'])),
                'expectedCompilationMetadata' => (new CompilationMetadata())
                    ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1'])),
            ],
        ];
    }

    public function testWithCompilationMetadata()
    {
        $emptyCompilationMetadata = new CompilationMetadata();
        $compilationMetadata = (new CompilationMetadata())
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['1']));

        $compilableSource = new CompilableSource([]);
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getCompilationMetadata());

        $compilableSource = $compilableSource->withCompilationMetadata($compilationMetadata);
        $this->assertSame($compilationMetadata, $compilableSource->getCompilationMetadata());

        $compilableSource = $compilableSource->withCompilationMetadata($emptyCompilationMetadata);
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getCompilationMetadata());
    }

    public function testMergeCompilationData()
    {
        $emptyCompilationMetadata = new CompilationMetadata();
        $compilationMetadata1 = (new CompilationMetadata())
            ->withClassDependencies(new ClassDependencyCollection([
                new ClassDependency('class1'),
            ]));

        $compilationMetadata2 = (new CompilationMetadata())
            ->withClassDependencies(new ClassDependencyCollection([
                new ClassDependency('class2'),
            ]))
            ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1']))
            ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport1']));

        $compilableSource = new CompilableSource([]);
        $this->assertEquals($emptyCompilationMetadata, $compilableSource->getCompilationMetadata());

        $compilableSource = $compilableSource->mergeCompilationData([
            $compilationMetadata1,
            $compilationMetadata2,
        ]);

        $this->assertEquals(
            (new CompilationMetadata())
                ->withClassDependencies(new ClassDependencyCollection([
                    new ClassDependency('class1'),
                    new ClassDependency('class2'),
                ]))
                ->withVariableDependencies(VariablePlaceholderCollection::createCollection(['variableDependency1']))
                ->withVariableExports(VariablePlaceholderCollection::createCollection(['variableExport1'])),
            $compilableSource->getCompilationMetadata()
        );
    }

    public function testToString()
    {
        $this->assertEquals('', (string) new CompilableSource([]));
        $this->assertEquals('statement1', (string) new CompilableSource(['statement1']));
        $this->assertEquals(
            'statement1' . "\n" . 'statement2',
            (string) new CompilableSource(['statement1', 'statement2'])
        );
    }
}
