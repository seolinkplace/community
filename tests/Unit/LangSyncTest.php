<?php

namespace Tests\Unit;

use Tests\TestCase;

class LangSyncTest extends TestCase
{
    private string $ukPath;
    private string $enPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ukPath = base_path('lang/uk');
        $this->enPath = base_path('lang/en');
    }

    private function loadKeys(string $path): array
    {
        $keys = [];
        foreach (glob($path . '/*.php') as $file) {
            $filename = basename($file, '.php');
            $data     = require $file;
            foreach (array_keys($this->flattenKeys($data)) as $key) {
                $keys[] = $filename . '.' . $key;
            }
        }
        sort($keys);
        return $keys;
    }

    private function flattenKeys(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenKeys($value, $fullKey));
            } else {
                $result[$fullKey] = $value;
            }
        }
        return $result;
    }

    private function langFiles(string $locale): array
    {
        return array_map(
            fn($f) => basename($f, '.php'),
            glob(base_path("lang/{$locale}/*.php"))
        );
    }

    public function test_uk_and_en_have_same_lang_files(): void
    {
        $ukFiles = $this->langFiles('uk');
        $enFiles = $this->langFiles('en');
        sort($ukFiles);
        sort($enFiles);
        $this->assertEquals($ukFiles, $enFiles, 'uk and en lang directories must contain the same files');
    }

    public function test_uk_and_en_have_same_keys(): void
    {
        $ukKeys = $this->loadKeys($this->ukPath);
        $enKeys = $this->loadKeys($this->enPath);
        $missingInEn = array_diff($ukKeys, $enKeys);
        $missingInUk = array_diff($enKeys, $ukKeys);
        $message = '';
        if ($missingInEn) {
            $message .= 'Keys in uk but missing in en: ' . implode(', ', $missingInEn) . "\n";
        }
        if ($missingInUk) {
            $message .= 'Keys in en but missing in uk: ' . implode(', ', $missingInUk) . "\n";
        }
        $this->assertEmpty(array_merge($missingInEn, $missingInUk), $message);
    }

    public function test_no_lang_file_has_syntax_errors(): void
    {
        foreach (['uk', 'en'] as $locale) {
            foreach (glob(base_path("lang/{$locale}/*.php")) as $file) {
                $output = shell_exec("php -l " . escapeshellarg($file) . " 2>&1");
                $this->assertStringContainsString(
                    'No syntax errors',
                    $output,
                    "Syntax error in lang/{$locale}/" . basename($file)
                );
            }
        }
    }

    public function test_no_empty_translation_values(): void
    {
        $empty = [];
        foreach (['uk', 'en'] as $locale) {
            foreach (glob(base_path("lang/{$locale}/*.php")) as $file) {
                $filename = basename($file, '.php');
                $data     = require $file;
                foreach ($this->flattenKeys($data) as $key => $value) {
                    if ($value === '' || $value === null) {
                        $empty[] = "{$locale}/{$filename}.{$key}";
                    }
                }
            }
        }
        $this->assertEmpty($empty, 'Empty translation values found: ' . implode(', ', $empty));
    }
}

// Додаємо окремий тест-клас для перевірки контролерів
