<?php namespace App\Http\Middleware;

use Closure;
use App;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Session;
use CustomFacades\Repositories\UserRepo;

class Referer {

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct()
    {

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
        $referer_id = $request->input('referer_id', null);

        if ( ! is_null($referer_id))
        {
            if (Session::get('referer_id') != $referer_id)
            {
                $user = UserRepo::find($referer_id);

                if ( ! empty($user) && $user->isReseller())
                {
                    Session::put('referer_id', $user->id);
                }
            }
        }

        return $next($request);
    }

}
