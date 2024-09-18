<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ServerActive {

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
		if (\CustomFacades\Server::isDisabled()) {
            if ($request->wantsJson())
                return response()->json(['status' => 0, 'error' => 'Server has been suspended, please contact administrator.'], 401);
            else
                return response()->view('disabled');
		}

		return $next($request);
	}

}
