<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PackageIdentityTest extends TestCase
{
    public function test_composer_package_identity_uses_apollo_sdk_naming(): void
    {
        $composer = $this->readComposer();

        self::assertSame('ometra/apollo-sdk', $composer['name']);
        self::assertSame('Ometra\\Apollo\\Sdk\\', array_key_first($composer['autoload']['psr-4']));
        self::assertSame(
            'Ometra\\Apollo\\Sdk\\Providers\\ApolloServiceProvider',
            $composer['extra']['laravel']['providers'][0],
        );
    }

    public function test_composer_identity_does_not_reference_legacy_package_or_namespace(): void
    {
        $composerRaw = file_get_contents(__DIR__.'/../../composer.json');

        self::assertIsString($composerRaw);
        self::assertStringNotContainsString('ometra/proteus-client', $composerRaw);
        self::assertStringNotContainsString('Ometra\\\\Apollo\\\\Proteus\\\\', $composerRaw);
        self::assertStringNotContainsString('ProteusServiceProvider', $composerRaw);
    }

    public function test_composer_only_declares_runtime_dependencies_used_by_the_sdk(): void
    {
        $composer = $this->readComposer();

        self::assertSame('Modular Laravel SDK for the Apollo suite APIs', $composer['description']);
        self::assertArrayNotHasKey('illuminate/database', $composer['require']);
        self::assertArrayNotHasKey('league/flysystem-aws-s3-v3', $composer['require']);
        self::assertArrayNotHasKey('guzzlehttp/guzzle', $composer['require']);
    }

    public function test_package_exposes_the_apollo_session_configuration_wrapper_and_preset(): void
    {
        $composer = $this->readComposer();
        $validator = 'bin/validate-apollo-session-config';

        self::assertSame('^8.7.0', $composer['require']['ometra/caronte-sdk']);
        self::assertContains($validator, $composer['bin']);
        self::assertFileExists(__DIR__.'/../../'.$validator);
        self::assertFileExists(__DIR__.'/../../config/group-session-config.json');
    }

    public function test_package_and_ci_require_php_84_or_newer(): void
    {
        $composer = $this->readComposer();
        $workflow = file_get_contents(__DIR__.'/../../.github/workflows/quality-gate.yml');

        self::assertSame('^8.4', $composer['require']['php']);
        self::assertIsString($workflow);
        self::assertStringContainsString("php-version: '8.4'", $workflow);
        self::assertStringNotContainsString('"8.2"', $workflow);
        self::assertStringNotContainsString('"8.3"', $workflow);
    }

    /**
     * @return array<string, mixed>
     */
    private function readComposer(): array
    {
        $content = file_get_contents(__DIR__.'/../../composer.json');
        self::assertIsString($content);

        $decoded = json_decode($content, true);
        self::assertIsArray($decoded);

        return $decoded;
    }
}
