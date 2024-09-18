<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ActiveSubscription {

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
	    $message = null;

        if (Auth::User()->manager_id && Auth::User()->manager && Auth::User()->manager->isExpired())
            $message = trans('front.manager_subscription_expired');

        if (Auth::User()->isExpired())
            $message = trans('front.subscription_expired');

        if (is_null($message))
            return $next($request);


        if ( config('tobuli.api') )
            return response()->json(['status' => 0, 'message' => $message], 401);

        if (isPublic()) {
            $email = Auth::User()->email;
            Auth::logout();
            return redirect(config('tobuli.frontend_subscriptions').'?subscription_expired&email='.base64_encode($email).'&server='.config('app.server'));
        }

        if (!is_null(Auth::User()->billing_plan_id)) {
            return redirect(route('payments.subscriptions'))->with(['message' => $message]);
        }

        Auth::logout();

        return redirect(route('login'))->with(['message' => $message]);

	}
}
