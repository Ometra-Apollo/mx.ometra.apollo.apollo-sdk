<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FrontendRouteContractTest extends TestCase
{
    public function test_frontend_route_prefix_has_one_stable_source_of_truth(): void
    {
        $config = require __DIR__.'/../../config/apollo.php';
        $routes = file_get_contents(__DIR__.'/../../src/routes/web.php');
        $component = file_get_contents(__DIR__.'/../../resources/js/shared/DirectoryTree/DirectoryTree.tsx');

        self::assertArrayNotHasKey('route_prefix', $config['frontend']);
        self::assertIsString($routes);
        self::assertStringContainsString("Route::prefix('_apollo')", $routes);
        self::assertIsString($component);
        self::assertStringContainsString('/_apollo/proteus/directories', $component);
    }

    public function test_app_menu_has_one_environment_aware_url_builder(): void
    {
        $config = file_get_contents(__DIR__.'/../../resources/js/shared/AppMenu/appMenu.config.tsx');
        $utils = file_get_contents(__DIR__.'/../../resources/js/shared/AppMenu/appMenu.utils.ts');
        $index = file_get_contents(__DIR__.'/../../resources/js/shared/AppMenu/index.ts');

        self::assertIsString($config);
        self::assertStringNotContainsString('function buildAppUrl', $config);
        self::assertIsString($utils);
        self::assertSame(1, substr_count($utils, 'function buildAppUrl'));
        self::assertStringContainsString("/_apollo/suite/applications/user", $utils);
        self::assertIsString($index);
        self::assertStringContainsString("export { buildAppUrl } from './appMenu.utils'", $index);
    }

    public function test_directory_tree_exposes_http_failures_instead_of_swallowing_them(): void
    {
        $component = file_get_contents(__DIR__.'/../../resources/js/shared/DirectoryTree/DirectoryTree.tsx');

        self::assertIsString($component);
        self::assertSame(2, substr_count($component, 'if (!response.ok)'));
        self::assertStringContainsString('setDirectoriesError(', $component);
        self::assertStringContainsString('setFolderError(', $component);
        self::assertStringNotContainsString('} catch {', $component);
        self::assertStringContainsString('role="alert"', $component);
    }
}
