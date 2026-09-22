<?php

namespace App\Services\Media;

use App\Models\ProductMedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProductMediaVariantGenerator
{
    public const WIDTHS = [320, 640, 960, 1280];

    public function generate(ProductMedia $media, bool $force = false): int
    {
        if (! str_starts_with($media->mime_type, 'image/') || $media->mime_type === 'image/svg+xml') {
            return 0;
        }

        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            throw new RuntimeException('GD with WebP support is required to generate responsive media.');
        }

        $source = $this->sourceContents($media);
        $image = @imagecreatefromstring($source);

        if ($image === false) {
            throw new RuntimeException("Unable to decode product media: {$media->path}");
        }

        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);
        $disk = config('kabulfit.media.derivative_disk', 'public');
        $generated = 0;

        foreach (self::WIDTHS as $width) {
            if ($width > $sourceWidth) {
                continue;
            }

            $height = max(1, (int) round($sourceHeight * ($width / $sourceWidth)));
            $resized = imagecreatetruecolor($width, $height);

            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $width, $height, $transparent);

            imagecopyresampled(
                $resized,
                $image,
                0,
                0,
                0,
                0,
                $width,
                $height,
                $sourceWidth,
                $sourceHeight,
            );

            $generated += $this->writeDerivative($media, $resized, $disk, 'webp', $width, $height, $force);

            if (function_exists('imageavif')) {
                $generated += $this->writeDerivative($media, $resized, $disk, 'avif', $width, $height, $force);
            }

            imagedestroy($resized);
        }

        imagedestroy($image);

        return $generated;
    }

    private function writeDerivative(
        ProductMedia $media,
        mixed $image,
        string $disk,
        string $format,
        int $width,
        int $height,
        bool $force,
    ): int {
        $existing = $media->derivatives()
            ->where('format', $format)
            ->where('width', $width)
            ->first();

        if ($existing && ! $force && Storage::disk($existing->disk)->exists($existing->path)) {
            return 0;
        }

        $directory = trim((string) config('kabulfit.media.derivative_directory', 'media/products'), '/');
        $path = sprintf(
            '%s/%s/%d.%s',
            $directory,
            sha1($media->product_id.'|'.$media->path),
            $width,
            $format,
        );

        $temporary = tempnam(sys_get_temp_dir(), 'kabulfit-media-');

        if ($temporary === false) {
            throw new RuntimeException('Unable to allocate temporary media file.');
        }

        try {
            $ok = $format === 'avif'
                ? imageavif($image, $temporary, (int) config('kabulfit.media.avif_quality', 58))
                : imagewebp($image, $temporary, (int) config('kabulfit.media.webp_quality', 78));

            if (! $ok) {
                throw new RuntimeException("Unable to encode {$format} derivative.");
            }

            $contents = file_get_contents($temporary);

            if ($contents === false) {
                throw new RuntimeException('Unable to read generated media derivative.');
            }

            Storage::disk($disk)->put($path, $contents, ['visibility' => 'public']);

            $media->derivatives()->updateOrCreate(
                ['format' => $format, 'width' => $width],
                [
                    'disk' => $disk,
                    'path' => $path,
                    'height' => $height,
                    'byte_size' => strlen($contents),
                ],
            );
        } finally {
            @unlink($temporary);
        }

        return 1;
    }

    private function sourceContents(ProductMedia $media): string
    {
        $publicPath = public_path($media->path);

        if (is_file($publicPath)) {
            $contents = file_get_contents($publicPath);

            if ($contents !== false) {
                return $contents;
            }
        }

        if (Str::startsWith($media->path, 'storage/')) {
            $relative = Str::after($media->path, 'storage/');

            if (Storage::disk('public')->exists($relative)) {
                return Storage::disk('public')->get($relative);
            }
        }

        throw new RuntimeException("Source media is not locally readable: {$media->path}");
    }
}
