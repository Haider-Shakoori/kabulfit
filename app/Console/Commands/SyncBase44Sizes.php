<?php

namespace App\Console\Commands;

use App\Models\LegacyProductRedirect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncBase44Sizes extends Command
{
    protected $signature = 'kabulfit:sync-base44-sizes
        {snapshot : Private local Base44 catalog export JSON}
        {--expected-total=141 : Expected original product count}
        {--expected-active=130 : Expected active original product count}
        {--commit : Persist validated size options (dry-run by default)}';

    protected $description = 'Backfill original Base44 size options without modifying stocks, prices or images';

    public function handle(): int
    {
        $path = (string) $this->argument('snapshot');
        if (! is_file($path) || ! is_readable($path)) {
            $this->error('Provide a readable private local Base44 snapshot.');

            return self::FAILURE;
        }

        try {
            $snapshot = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->error('Invalid snapshot: '.$e->getMessage());

            return self::FAILURE;
        }

        $source = $snapshot['products'] ?? null;
        if (! is_array($source)) {
            $this->error('Missing products array.');

            return self::FAILURE;
        }

        $active = count(array_filter($source, fn (array $p): bool => (bool) ($p['is_active'] ?? false)));
        if (count($source) !== (int) $this->option('expected-total') ||
            $active !== (int) $this->option('expected-active')) {
            $this->error('Source counts differ from verified original export.');

            return self::FAILURE;
        }

        $updates = [];
        foreach ($source as $row) {
            $id = (string) ($row['id'] ?? '');
            $sizes = $row['sizes'] ?? null;
            if ($id === '' || ! is_array($sizes)) {
                $this->error('Missing source ID or sizes array.');

                return self::FAILURE;
            }
            $normalized = array_values(array_unique(array_map(
                fn ($size): string => strtoupper(trim((string) $size)), $sizes,
            )));

            foreach ($normalized as $size) {
                if (preg_match('/^[A-Z0-9-]{1,20}$/', $size) !== 1) {
                    $this->error('Invalid original size option for '.$id);

                    return self::FAILURE;
                }
            }
            $product = LegacyProductRedirect::query()
                ->where('legacy_key', $id)->with('product')->first()?->product;
            if (! $product || isset($updates[$product->id])) {
                $this->error('Missing or duplicated imported legacy product '.$id);

                return self::FAILURE;
            }

            $existing = $product->size_options ?? [];
            if ($existing !== [] && $existing !== $normalized) {
                $this->error('Refusing to overwrite customized product sizes for '.$id);

                return self::FAILURE;
            }
            $updates[$product->id] = $normalized;
        }

        $this->info('Validated '.count($updates).' original size-option records; '.$active.' active products.');
        if (! $this->option('commit')) {
            $this->comment('Dry-run complete. Pass --commit only on backed-up staging.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($updates): void {
            foreach ($updates as $id => $sizes) {
                \App\Models\Product::query()->whereKey($id)->firstOrFail()
                    ->update(['size_options' => $sizes]);
            }
        });

        $this->info('Updated original size options; pricing, inventory and media unchanged.');

        return self::SUCCESS;
    }
}
