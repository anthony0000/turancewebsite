<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackPageVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        try {
            static $tableReady;

            if ($tableReady === null) {
                $tableReady = Schema::hasTable('page_visits');
            }

            if (! $tableReady) {
                return $response;
            }

            PageVisit::query()->create([
                'path' => $request->getPathInfo(),
                'route_name' => $request->route()?->getName(),
                'page_group' => $this->resolvePageGroup($request),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
                'referrer' => Str::limit((string) $request->headers->get('referer'), 1000, ''),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (app()->runningInConsole()) {
            return false;
        }

        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return false;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return false;
        }

        if ($request->is('admin') || $request->is('admin/*') || $request->is('up')) {
            return false;
        }

        $routeName = (string) $request->route()?->getName();

        if (str_starts_with($routeName, 'admin.')) {
            return false;
        }

        if ($response->isRedirection() || $response->getStatusCode() >= 400) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return $contentType === '' || str_contains($contentType, 'text/html');
    }

    private function resolvePageGroup(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();

        return match (true) {
            $routeName === 'home' => 'Landing',
            $routeName === 'contact.show' => 'Contact',
            $routeName === 'service.show', str_starts_with($routeName, 'services.') => 'Services',
            default => 'Marketing',
        };
    }
}
