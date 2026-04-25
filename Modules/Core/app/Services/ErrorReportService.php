<?php

namespace Modules\Core\Services;

use Modules\Core\Models\ErrorReport;
use Illuminate\Http\Request;
use Throwable;

class ErrorReportService
{
    private const IGNORED_EXCEPTIONS = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Http\Exceptions\ThrottleRequestsException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
    ];

    public function shouldReport(Throwable $e): bool
    {
        foreach (self::IGNORED_EXCEPTIONS as $ignored) {
            if ($e instanceof $ignored) {
                return false;
            }
        }
        return true;
    }

    public function store(Throwable $e, ?Request $request = null): ?ErrorReport
    {
        try {
            $input = $this->sanitizeInput($request?->except(['password', 'password_confirmation', 'token']));

            $userId   = null;
            $userType = null;

            if ($request && auth('unified')->check()) {
                $userId   = auth('unified')->id();
                $userType = 'unified';
            }

            return ErrorReport::create([
                'message'         => mb_substr($e->getMessage(), 0, 255),
                'exception_class' => get_class($e),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
                'trace'           => mb_substr($e->getTraceAsString(), 0, 65000),
                'url'             => $request?->fullUrl(),
                'method'          => $request?->method(),
                'input'           => $input,
                'ip'              => $request?->ip(),
                'user_agent'      => mb_substr($request?->userAgent() ?? '', 0, 255),
                'user_id'         => $userId,
                'user_type'       => $userType,
                'status'          => 'new',
            ]);
        } catch (Throwable) {
            return null;
        }
    }

    private function sanitizeInput(?array $input): ?array
    {
        if (empty($input)) {
            return null;
        }

        return array_map(function ($value) {
            if (is_string($value) && mb_strlen($value) > 500) {
                return mb_substr($value, 0, 500) . '…';
            }
            return $value;
        }, $input);
    }
}
