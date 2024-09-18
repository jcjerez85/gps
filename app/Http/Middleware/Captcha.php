<?php

namespace App\Http\Middleware;

use Closure;
use Tobuli\Exceptions\ValidationException;

class Captcha {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $captchaService = app('captchaService');

        if (! $captchaService->isValid()) {
            if ($request->ajax()) {
                throw new ValidationException($captchaService->getMessages());
            }

            return redirect()->back()
                ->withInput()
                ->withMessage(implode('\n', $captchaService->getMessages()));
        }

        return $next($request);
    }
}
