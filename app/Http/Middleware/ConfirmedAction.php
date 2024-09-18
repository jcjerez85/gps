<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tobuli\ConfirmedAction\Prompt;

class ConfirmedAction
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Prompt::isAvailable($request)) {
            return Prompt::makeFromRequest($request)
                ->setRespondJson($request->wantsJson())
                ->buildResponse();
        }

        return $next($request);
    }
}
