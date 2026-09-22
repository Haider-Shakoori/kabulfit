<?php

namespace App\Jobs;

use App\Models\ProductMedia;
use App\Services\Media\ProductMediaVariantGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateProductMediaDerivatives implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public readonly int $productMediaId,
        public readonly bool $force = false,
    ) {}

    public function handle(ProductMediaVariantGenerator $generator): void
    {
        $media = ProductMedia::query()->find($this->productMediaId);

        if (! $media) {
            return;
        }

        $generator->generate($media, $this->force);
    }
}
