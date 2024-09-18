<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ApiActive {

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
    public function __construct()
    {
        Config::set('tobuli.api', 1);
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
		if (\CustomFacades\Server::isApiDisabled()) {
            return response()->json(['status' => 0, 'error' => 'Your server API is disabled due to unpaid invoices.'], 401);
		}

		return $next($request);
	}

}
