<?php

namespace App\Http\Middleware;

use Closure;
use CustomFacades\Appearance;
use Language;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Session;

class SetLang
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
        if ($request->hasSession() && $request->session()->has('language')) {
            $lang = $request->session()->get('language');
        }

        if ($request->has('lang')) {
            $lang = $request->get('lang');
        }

        if (empty($lang)) {
            $lang = Appearance::resolveUser()->getSetting('default_language');
        } else {
            Language::setLangKey($lang);
        }

        Language::set($lang);

        return $next($request);
    }
}
