<?php

namespace Tests\Unit;

use Tests\TestCase;

class ModuleControllersTest extends TestCase
{
    private function getModuleControllers(): array
    {
        $controllers = [];
        $modulesPath = base_path('Modules');

        foreach (glob($modulesPath . '/*/app/Http/Controllers/**/*.php') as $file) {
            $controllers[] = $file;
        }
        foreach (glob($modulesPath . '/*/app/Http/Controllers/*.php') as $file) {
            $controllers[] = $file;
        }

        return array_unique($controllers);
    }

    public function test_all_module_controllers_have_controller_use_statement(): void
    {
        $missing = [];

        foreach ($this->getModuleControllers() as $file) {
            $content = file_get_contents($file);
            if (!str_contains($content, 'extends Controller')) {
                continue;
            }
            if (!str_contains($content, 'use App\Http\Controllers\Controller')) {
                $missing[] = str_replace(base_path() . '/', '', $file);
            }
        }

        $this->assertEmpty(
            $missing,
            'Controllers missing "use App\Http\Controllers\Controller": ' . implode(', ', $missing)
        );
    }

    public function test_all_module_controllers_have_valid_syntax(): void
    {
        $errors = [];

        foreach ($this->getModuleControllers() as $file) {
            $output = shell_exec('php -l ' . escapeshellarg($file) . ' 2>&1');
            if (!str_contains($output, 'No syntax errors')) {
                $errors[] = str_replace(base_path() . '/', '', $file) . ': ' . trim($output);
            }
        }

        $this->assertEmpty(
            $errors,
            'Controllers with syntax errors: ' . implode("\n", $errors)
        );
    }

    public function test_no_legacy_auth_guards_in_modules(): void
    {
        $patterns = ["auth('client')", "auth('webmaster')", "Auth::guard('client')", "Auth::guard('webmaster')"];
        $found    = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(base_path('Modules'))
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $content = file_get_contents($file->getPathname());
            foreach ($patterns as $pattern) {
                if (str_contains($content, $pattern)) {
                    $found[] = str_replace(base_path() . '/', '', $file->getPathname()) . ": {$pattern}";
                }
            }
        }

        $this->assertEmpty(
            $found,
            "Legacy auth guards found:\n" . implode("\n", $found)
        );
    }

    public function test_no_legacy_request_user_guards_in_modules(): void
    {
        $patterns = [
            "user('client')",
            "user('webmaster')",
            "request()->user('client')",
            "request()->user('webmaster')",
        ];
        $found = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(base_path('Modules'))
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $content = file_get_contents($file->getPathname());
            foreach ($patterns as $pattern) {
                if (str_contains($content, $pattern)) {
                    $found[] = str_replace(base_path() . '/', '', $file->getPathname()) . ": {$pattern}";
                }
            }
        }

        $this->assertEmpty(
            $found,
            "Legacy request user guards found:\n" . implode("\n", $found)
        );
    }

}
