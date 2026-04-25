<?php

namespace Modules\Core\Services;

use Illuminate\Http\Request;

class LocaleService
{
    /** @var array<string, array> */
    private array $locales;

    public function __construct()
    {
        $this->locales = config('locales.locales', []);
    }

    /**
     * All supported locale codes.
     */
    public function supported(): array
    {
        return array_keys($this->locales);
    }

    /**
     * All locale configs.
     */
    public function all(): array
    {
        return $this->locales;
    }

    /**
     * Primary locales (shown as direct buttons in switcher).
     */
    public function primary(): array
    {
        return array_filter($this->locales, fn($l) => $l['primary']);
    }

    /**
     * Secondary locales (shown in dropdown).
     */
    public function secondary(): array
    {
        return array_filter($this->locales, fn($l) => !$l['primary']);
    }

    /**
     * Check if locale is supported.
     */
    public function isSupported(string $locale): bool
    {
        return isset($this->locales[$locale]);
    }

    /**
     * URL prefix for a locale. Empty string for default (uk).
     */
    public function prefix(string $locale): string
    {
        return $this->locales[$locale]['prefix'] ?? '';
    }

    /**
     * Resolve locale from URL prefix segment.
     * Returns null if not matched.
     */
    public function fromPrefix(string $prefix): ?string
    {
        foreach ($this->locales as $locale => $config) {
            if ($config['prefix'] === $prefix) {
                return $locale;
            }
        }
        return null;
    }

    /**
     * Detect best locale from Accept-Language header.
     * Falls back to 'en', then 'uk'.
     */
    public function detectFromRequest(Request $request): string
    {
        $header = $request->header('Accept-Language', '');
        // Parse "uk-UA,uk;q=0.9,en-US;q=0.8,en;q=0.7"
        $tags = preg_split('/,\s*/', $header);
        foreach ($tags as $tag) {
            $lang = strtolower(substr(trim($tag), 0, 2));
            if ($this->isSupported($lang)) {
                return $lang;
            }
        }
        return 'en';
    }

    /**
     * Public URL for a given locale and path.
     * Path should start with '/'.
     */
    public function urlForLocale(string $locale, string $path = '/'): string
    {
        $prefix = $this->prefix($locale);
        if ($prefix === '') {
            return $path;
        }
        return '/' . $prefix . ($path === '/' ? '/' : $path);
    }
}
