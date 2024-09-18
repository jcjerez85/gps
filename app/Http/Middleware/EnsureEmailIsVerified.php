<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Entities\User;

class EnsureEmailIsVerified
{
    /**
     * The URIs that should be excluded from verification.
     *
     * @var array
     */
    protected $except = [
        'authentication/create',
        'login',
        'logout',
        'verification*',
        '_debugbar/*'
    ];

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = $this->auth->user();

        if (!$user)
            return $next($request);

        if ($user->hasVerifiedEmail())
            return $next($request);

        if (!settings('main_settings.email_verification'))
            return $next($request);

        if ($this->inExceptArray($request))
            return $next($request);

        $user->sendEmailVerificationNotification();

        if ($request->expectsJson() || $request->ajax())
            throw new AuthorizationException(trans('front.please_verify_email'), 403);

        return Redirect::route('verification')->with('url', $request->fullUrl());
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
