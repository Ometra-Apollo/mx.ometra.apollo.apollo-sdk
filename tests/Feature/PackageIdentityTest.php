<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PackageIdentityTest extends TestCase
{
    public function testComposerPackageIdentityUsesApolloSdkNaming(): void
    {
        $composer = $this->readComposer();

        self::assertSame('ometra/apollo-sdk', $composer['name']);
        self::assertSame('Ometra\\Apollo\\Sdk\\', array_key_first($composer['autoload']['psr-4']));
        self::assertSame(
            'Ometra\\Apollo\\Sdk\\Providers\\ApolloServiceProvider',
            $composer['extra']['laravel']['providers'][0],
        );
    }

    public function testComposerIdentityDoesNotReferenceLegacyPackageOrNamespace(): void
    {
        $composerRaw = file_get_contents(__DIR__ . '/../../composer.json');

        self::assertIsString($composerRaw);
        self::assertStringNotContainsString('ometra/proteus-client', $composerRaw);
        self::assertStringNotContainsString('Ometra\\\\Apollo\\\\Proteus\\\\', $composerRaw);
        self::assertStringNotContainsString('ProteusServiceProvider', $composerRaw);
    }

    /**
     * @return array<string, mixed>
     */
    private function readComposer(): array
    {
        $content = file_get_contents(__DIR__ . '/../../composer.json');
        self::assertIsString($content);

        $decoded = json_decode($content, true);
        self::assertIsArray($decoded);

        return $decoded;
    }
}
