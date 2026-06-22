<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DocumentationExamplesTest extends TestCase
{
    #[DataProvider('documentationFiles')]
    public function testDocumentationUsesApolloPackageConfigAndModularExamples(string $relativePath): void
    {
        $contents = self::readProjectFile($relativePath);

        self::assertStringContainsString('ometra/apollo-sdk', $contents, $relativePath . ' must document the Apollo package name.');
        self::assertStringContainsString('config/apollo.php', $contents, $relativePath . ' must document Apollo config.');
        self::assertStringContainsString('PROTEUS_BASE_URL', $contents, $relativePath . ' must document Proteus module base URL.');
        self::assertStringContainsString('PULSE_BASE_URL', $contents, $relativePath . ' must document Pulse module base URL.');
        self::assertStringContainsString('FLARE_BASE_URL', $contents, $relativePath . ' must document Flare module base URL.');
        self::assertStringContainsString('IGNIS_BASE_URL', $contents, $relativePath . ' must document Ignis module base URL.');
        self::assertStringContainsString('Apollo::proteus()->media()->index(', $contents, $relativePath . ' must show contextual Proteus resource usage.');
    }

    #[DataProvider('documentationFiles')]
    public function testDocumentationDoesNotAdvertiseLegacyProteusApi(string $relativePath): void
    {
        $contents = self::readProjectFile($relativePath);

        foreach (self::legacyDocumentationFragments() as $fragment) {
            self::assertStringNotContainsString($fragment, $contents, $relativePath . ' still documents legacy fragment: ' . $fragment);
        }
    }

    public function testDocumentationMentionsAutomaticErrorPages(): void
    {
        $readme = self::readProjectFile('README.md');
        $contract = self::readProjectFile('docs/api-contract.md');

        foreach ([$readme, $contract] as $contents) {
            self::assertStringContainsString('apollo-error-pages', $contents);
            self::assertStringContainsString('APOLLO_ERROR_PAGES_ENABLED', $contents);
        }
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function documentationFiles(): array
    {
        return [
            'readme' => ['README.md'],
            'api contract' => ['docs/api-contract.md'],
            'changelog' => ['CHANGELOG.md'],
            'breaking changes' => ['BREAKING_CHANGES.md'],
            'release notes' => ['RELEASE_NOTES.md'],
        ];
    }

    /**
     * @return list<string>
     */
    private static function legacyDocumentationFragments(): array
    {
        return [
            'ometra/proteus-client',
            'Ometra\\Apollo\\Proteus',
            'Proteus::',
            'ProteusServiceProvider',
            'config/proteus.php',
            'proteus-config',
            'mediaIndex',
            'metadataKeys',
            'categoriesIndex',
            'PROTEUS_APP_TOKEN',
            'PROTEUS_APP_NAME',
            'APOLLO_PROTEUS_BASE_URL',
            'CARONTE_APP_CN',
        ];
    }

    private static function readProjectFile(string $relativePath): string
    {
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $relativePath;

        self::assertFileExists($path, $relativePath . ' must exist.');

        return (string) file_get_contents($path);
    }
}
