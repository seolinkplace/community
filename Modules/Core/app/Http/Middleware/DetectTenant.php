<?php
namespace Modules\Core\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class DetectTenant
{
    public function handle(Request $request, Closure $next)
    {
        // webmasters.seolinkplace.com → редірект на /wm/ (nginx вже робить це, але на випадок)
        $host = $request->getHost();
        if (str_starts_with($host, 'webmasters.')) {
            return redirect('https://seolinkplace.com/wm/' . $request->path());
        }
        return $next($request);
    }
}
