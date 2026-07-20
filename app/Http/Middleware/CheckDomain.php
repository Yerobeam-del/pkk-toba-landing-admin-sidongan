<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $domain
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $domain)
    {
        $currentHost = $request->getHost();

        if ($currentHost !== $domain) {
            // Redirect ke domain yang benar
            return redirect()->away('https://' . $domain . $request->path());
        }

        return $next($request);
    }
}
