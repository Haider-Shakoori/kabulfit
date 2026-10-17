<?php

namespace Tests\Feature;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class AlpineMarkupSafetyTest extends TestCase
{
    public function test_blade_templates_do_not_use_single_quoted_x_data_attributes(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(resource_path('views')),
        );

        $offenders = [];

        foreach ($iterator as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            if ($contents !== false && str_contains($contents, "x-data='")) {
                $offenders[] = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file->getPathname());
            }
        }

        $this->assertSame(
            [],
            $offenders,
            'Single-quoted x-data can be terminated by Laravel @js()/Js::from() output. Use double-quoted x-data and single-quoted JavaScript strings instead.',
        );
    }
}
