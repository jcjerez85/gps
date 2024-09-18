<?php namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Tobuli\Entities\UserSecondaryCredentials;
use Tobuli\Services\ScheduleService;

class Authenticate {

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
	public function handle(Request $request, Closure $next)
	{
		if ($this->auth->guest())
		{
            return $this->redirect($request);
		}

		if ( ! Auth::User()->active)
		{
            return $this->redirect($request);
		}

        if ($secondaryCredId = $request->session()->get('secondary_cred_id')) {
            $credCacheKey = 'secondary_cred_' . $secondaryCredId;

            $secondaryCred = Cache::remember($credCacheKey, 300, fn() => UserSecondaryCredentials::find($secondaryCredId));

            if ($secondaryCred === null) {
                Auth::logout();

                return $this->redirect($request);
            }

            Auth::user()->setLoginSecondaryCredentials($secondaryCred);
        }

		if ($request->session()->has('hash')) {
            if ($request->session()->get('hash') !== Auth::User()->password_hash)
            {
                Auth::logout();

                return $this->redirect($request);
            }
        } else {
            $request->session()->put('hash', Auth::User()->password_hash);
        }

        if ($message = $this->checkLoginPeriods()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response($message, 401);
            }

            return redirect(route('login'))->with(['message' => $message]);
        }

        setActingUser(Auth::User());

		return $next($request);
	}

    public function terminate($request, $response)
    {
        if ($user = Auth::User()) {
            $user->loged_at = date('Y-m-d H:i:s');
            $user->save();
        }
    }

    private function checkLoginPeriods(): string
    {
        if (settings('login_periods.enabled')
            && Auth::User()->login_periods
            && ($scheduleService = new ScheduleService(Auth::User()->login_periods ?? []))->outSchedules(Carbon::now())
        ) {
            Auth::logout();

            return trans('front.login_time_restricted_until', [
                'datetime' => $scheduleService->closestScheduleTime(Carbon::now())
            ]);
        }

        return '';
    }

	private function redirect(Request $request)
    {
        if ( ! $this->auth->guest())
            $this->auth->logout();

        if ($request->ajax())
            return response('Unauthorized.', 401);

        if (isPublic()) {
            return redirect()->guest(config('tobuli.frontend_login').'/?server='.config('app.server'));
        }

        $request->session()->forget('login_redirect');
        $request->session()->put('login_redirect', $request->getRequestUri());

        return redirect()->guest(route('home'));
    }
}
