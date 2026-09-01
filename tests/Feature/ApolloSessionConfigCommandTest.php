<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApolloSessionConfigCommandTest extends TestCase
{
    public function test_wrapper_uses_the_bundled_preset_for_an_apollo_workspace(): void
    {
        $workspace = sys_get_temp_dir().'/apollo-session-config-'.bin2hex(random_bytes(8));
        $session = implode(PHP_EOL, [
            'SESSION_DRIVER=redis',
            'SESSION_CONNECTION=session',
            'SESSION_STORE=session',
            'SESSION_DOMAIN=.apollo.ometra.mx',
            'SESSION_SECURE_COOKIE=true',
            'SESSION_ENCRYPT=true',
            'SESSION_COOKIE=apollo-session',
        ]).PHP_EOL;
        $caronte = implode(PHP_EOL, [
            'CARONTE_SESSION_KEY=apollo-session',
            'CARONTE_TOKEN_CLOCK_SKEW_SECONDS=60',
            'CARONTE_TOKEN_REFRESH_LEEWAY_SECONDS=60',
        ]).PHP_EOL;

        foreach (['aeris', 'flare', 'ignis', 'lume', 'proteus', 'pulse'] as $application) {
            $environmentDirectory = $workspace.'/mx.ometra.apollo.'.$application.'/.env.d';
            mkdir($environmentDirectory, 0777, true);
            file_put_contents($environmentDirectory.'/01-app.env', "APP_ENV=production\n");
            file_put_contents($environmentDirectory.'/04-session.env', $session);
            file_put_contents($environmentDirectory.'/09-caronte.env', $caronte);
        }

        $command = [
            PHP_BINARY,
            __DIR__.'/../../bin/validate-apollo-session-config',
            '--workspace',
            $workspace,
        ];
        $pipes = [];
        $environment = array_merge($_ENV, [
            'APOLLO_CARONTE_AUTOLOAD' => dirname(__DIR__, 4).'/Ometra-Core/Caronte/mx.ometra.caronte-sdk/vendor/autoload.php',
        ]);
        $process = proc_open(
            $command,
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            __DIR__.'/../..',
            $environment,
        );
        self::assertIsResource($process);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($process);
        $this->removeDirectory($workspace);

        self::assertSame(0, $exit, is_string($stderr) ? $stderr : 'Unable to read stderr.');
        self::assertIsString($stdout);
        self::assertStringContainsString('Apollo shared-session configuration is consistent across 6 applications', $stdout);
        self::assertSame('', $stderr);
    }

    private function removeDirectory(string $directory): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($directory);
    }
}
