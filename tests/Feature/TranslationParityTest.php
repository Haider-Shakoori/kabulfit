<?php

namespace Tests\Feature;

use Tests\TestCase;

class TranslationParityTest extends TestCase
{
    public function test_dari_and_pashto_have_the_same_translation_keys_as_english(): void
    {
        $files = glob(base_path('lang/en/*.php')) ?: [];

        $this->assertNotEmpty($files);

        foreach ($files as $englishPath) {
            $file = basename($englishPath);
            $dariPath = base_path('lang/fa/'.$file);
            $pashtoPath = base_path('lang/ps/'.$file);

            $this->assertFileExists($dariPath, 'Missing Dari language file: '.$file);
            $this->assertFileExists($pashtoPath, 'Missing Pashto language file: '.$file);

            $english = require $englishPath;
            $dari = require $dariPath;
            $pashto = require $pashtoPath;

            $englishKeys = $this->flattenKeys($english);
            $dariKeys = $this->flattenKeys($dari);
            $pashtoKeys = $this->flattenKeys($pashto);

            sort($englishKeys);
            sort($dariKeys);
            sort($pashtoKeys);

            $this->assertSame($englishKeys, $dariKeys, $file.' has English/Dari translation-key drift.');
            $this->assertSame($englishKeys, $pashtoKeys, $file.' has English/Pashto translation-key drift.');
        }
    }

    public function test_base44_customer_facing_options_are_localized_in_all_supported_languages(): void
    {
        $keys = [
            'site.get_in_touch',
            'site.send_message',
            'site.newsletter',
            'site.subscribe',
            'site.frequent_questions',
            'commerce.payment_method',
            'commerce.pay_with_card',
            'commerce.pay_with_paypal',
            'commerce.paypal_error',
            'commerce.review_saved',
        ];

        foreach (['en', 'fa', 'ps'] as $locale) {
            foreach ($keys as $key) {
                $this->assertNotSame(
                    $key,
                    trans($key, [], $locale),
                    "Missing {$locale} translation for {$key}.",
                );
            }
        }
    }

    private function flattenKeys(array $values, string $prefix = ''): array
    {
        $keys = [];

        foreach ($values as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $keys = [...$keys, ...$this->flattenKeys($value, $path)];

                continue;
            }

            $keys[] = $path;
        }

        return $keys;
    }
}
