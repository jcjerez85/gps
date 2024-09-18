<?php

namespace App\Http\Middleware;

use Closure;
use CustomFacades\Appearance;
use Illuminate\Support\Facades\Auth;
use Language;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tobuli\Entities\User;

class TokenAuth
{

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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('addon.login_token') && $this->auth->guest() && $request->has('token')) {
            $user = User::where('login_token', $request->get('token'))->first();

            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
