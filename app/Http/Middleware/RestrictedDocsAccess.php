<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RestrictedDocsAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (Gate::allows('viewApiDocs')) {
            return $next($request);
        }
        abort(403);
    }
}