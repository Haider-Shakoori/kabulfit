<?php

use App\Jobs\GenerateProductMediaDerivatives;
use App\Models\ProductMedia;
use App\Services\Media\ProductMediaVariantGenerator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('media:generate-responsive {--sync} {--force}', function (): int {
    $sync = (bool) $this->option('sync');
    $force = (bool) $this->option('force');
    $processed = 0;
    $generated = 0;

    ProductMedia::query()
        ->where('mime_type', 'like', 'image/%')
        ->where('mime_type', '!=', 'image/svg+xml')
        ->orderBy('id')
        ->eachById(function (ProductMedia $media) use ($sync, $force, &$processed, &$generated): void {
            $processed++;

            if ($sync) {
                $generated += app(ProductMediaVariantGenerator::class)->generate($media, $force);

                return;
            }

            GenerateProductMediaDerivatives::dispatch($media->id, $force);
        });

    $this->info($sync
        ? "Processed {$processed} media records; generated {$generated} derivatives."
        : "Queued responsive generation for {$processed} media records.");

    return 0;
})->purpose('Generate WebP/AVIF responsive derivatives for raster product media.');

Artisan::command('ops:readiness {--strict}', function (): int {
    $strict = (bool) $this->option('strict') || app()->isProduction();
    $checks = [
        'APP_DEBUG is disabled' => ! config('app.debug'),
        'APP_URL uses HTTPS' => str_starts_with((string) config('app.url'), 'https://'),
        'Queue connection is asynchronous' => config('queue.default') !== 'sync',
        'Cache store is durable/shared' => ! in_array(config('cache.default'), ['array', 'null'], true),
        'Responsive media derivative disk is configured' => filled(config('kabulfit.media.derivative_disk')),
        'Configuration cache is built' => app()->configurationIsCached(),
        'Route cache is built' => app()->routesAreCached(),
    ];

    $failed = false;

    foreach ($checks as $label => $passed) {
        $this->{$passed ? 'info' : 'warn'}(($passed ? 'PASS ' : 'WARN ').$label);
        $failed = $failed || ! $passed;
    }

    if ($strict && $failed) {
        $this->error('Production readiness checks failed.');

        return 1;
    }

    return 0;
})->purpose('Verify production cache, queue, URL and media configuration.');

Schedule::command('commerce:expire-checkouts')
    ->everyFiveMinutes()
    ->onOneServer()
    ->withoutOverlapping(10);

Schedule::command('media:generate-responsive')
    ->dailyAt('02:20')
    ->onOneServer()
    ->withoutOverlapping(60);

Schedule::command('queue:prune-failed --hours=168')
    ->dailyAt('03:10')
    ->onOneServer()
    ->withoutOverlapping(30);
