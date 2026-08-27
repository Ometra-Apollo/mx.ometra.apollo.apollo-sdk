<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApolloSessionConfigCommandTest extends TestCase
{
    public function test_wrapper_uses_the_bundled_preset_against_the_apollo_workspace(): void
    {
        $command = [
            PHP_BINARY,
            __DIR__.'/../../bin/validate-apollo-session-config',
            '--workspace',
            dirname(__DIR__, 3),
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

        self::assertSame(0, $exit, is_string($stderr) ? $stderr : 'Unable to read stderr.');
        self::assertIsString($stdout);
        self::assertStringContainsString('Apollo shared-session configuration is consistent across 6 applications', $stdout);
        self::assertSame('', $stderr);
    }
}
