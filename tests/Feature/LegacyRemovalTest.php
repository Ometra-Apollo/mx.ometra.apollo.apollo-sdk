<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LegacyRemovalTest extends TestCase
{
    #[DataProvider('legacyArtifacts')]
    public function testLegacyProteusArtifactsArePhysicallyRemoved(string $relativePath): void
    {
        self::assertFileDoesNotExist(
            self::projectPath($relativePath),
            $relativePath . ' must be removed from the Apollo SDK clean-cut migration.',
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function legacyArtifacts(): array
    {
        return [
            'legacy root entrypoint' => ['src/Proteus.php'],
            'legacy facade' => ['src/Facades/Proteus.php'],
            'legacy service provider' => ['src/Providers/ProteusServiceProvider.php'],
            'legacy proteus config' => ['config/proteus.php'],
            'legacy proteus api client' => ['src/Api/ProteusApiClient.php'],
            'legacy media api' => ['src/Api/MediaApi.php'],
            'legacy metadata api' => ['src/Api/MetadataApi.php'],
            'legacy categories api' => ['src/Api/CategoriesApi.php'],
            'legacy directories api' => ['src/Api/DirectoriesApi.php'],
            'legacy presets api' => ['src/Api/PresetsApi.php'],
            'legacy proteus exception' => ['src/Exceptions/ProteusException.php'],
            'legacy api wrapper test' => ['tests/ApiWrappersTest.php'],
            'legacy proteus api client test' => ['tests/ProteusApiClientTest.php'],
            'legacy recording proteus client test double' => ['tests/RecordingProteusApiClient.php'],
            'hardcoded production test group' => ['src/Test/DummyGroup.php'],
        ];
    }

    public function testSourceTreeDoesNotContainLegacyProteusNamespaceOrProviderReferences(): void
    {
        $indexedFiles = self::phpAndConfigFilesFrom(['src', 'config', 'composer.json']);

        self::assertNotSame([], $indexedFiles, 'The source/config index must include files to make this scan meaningful.');

        foreach ($indexedFiles as $file) {
            $contents = (string) file_get_contents(self::projectPath($file));

            self::assertStringNotContainsString('Ometra\\Apollo\\Proteus', $contents, $file . ' contains the legacy namespace.');
            self::assertStringNotContainsString('ProteusServiceProvider', $contents, $file . ' references the legacy provider.');
            self::assertStringNotContainsString('config/proteus.php', $contents, $file . ' references the legacy config file.');
            self::assertStringNotContainsString('proteus-config', $contents, $file . ' references the legacy publish tag.');
        }
    }

    /**
     * @param  list<string>  $roots
     * @return list<string>
     */
    private static function phpAndConfigFilesFrom(array $roots): array
    {
        $files = [];

        foreach ($roots as $root) {
            $absoluteRoot = self::projectPath($root);

            if (is_file($absoluteRoot)) {
                $files[] = $root;
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($absoluteRoot, FilesystemIterator::SKIP_DOTS));

            foreach ($iterator as $fileInfo) {
                if (! $fileInfo instanceof SplFileInfo || ! $fileInfo->isFile()) {
                    continue;
                }

                $extension = $fileInfo->getExtension();

                if (! in_array($extension, ['php', 'json'], true)) {
                    continue;
                }

                $files[] = substr($fileInfo->getPathname(), strlen(self::projectPath()) + 1);
            }
        }

        sort($files);

        return $files;
    }

    private static function projectPath(string $relativePath = ''): string
    {
        $root = dirname(__DIR__, 2);

        return $relativePath === '' ? $root : $root . DIRECTORY_SEPARATOR . $relativePath;
    }
}
