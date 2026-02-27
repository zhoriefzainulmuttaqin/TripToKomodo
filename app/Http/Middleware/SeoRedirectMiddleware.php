<?php

namespace App\Http\Middleware;

use App\Models\SeoRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoRedirectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/' . ltrim($request->path(), '/');
        $uri = $request->getRequestUri();

        $redirect = SeoRedirect::query()
            ->where('is_active', true)
            ->where(function ($query) use ($path, $uri): void {
                $query->where('from_url', $path)->orWhere('from_url', $uri);
            })
            ->first();

        if ($redirect) {
            return redirect($redirect->to_url, $redirect->status_code);
        }

        return $next($request);
    }
}
