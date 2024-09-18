<?php namespace App\Http\Middleware;

use Closure;
use Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\User;
use Illuminate\Support\Facades\Auth;
use Tobuli\Entities\UserSecondaryCredentials;

class ApiAuthenticate {

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
        $user = null;
        $secondaryCred = null;

        if ($hash = $this->getApiHash($request)) {
            $user = User::where('api_hash', $hash)->first();

            if (isPublic()) {
                if (empty($user) || strtotime($user->api_hash_expire) < time()) {
                    $user = \CustomFacades\RemoteUser::getByApiHash($hash);
                }
            }

            if ($user === null && config('auth.secondary_credentials')) {
                $secondaryCred = UserSecondaryCredentials::where('api_hash', $hash)->first();
                $user = $secondaryCred->user ?? null;
            }
}

        if (empty($user))
            return response()->json(['status' => 0, 'message' => trans('front.login_failed')], 401);

        if ( ! $user->active)
            return response()->json(['status' => 0, 'message' => trans('front.login_suspended')], 401);

        Auth::onceUsingId($user->id);

        setActingUser(Auth::user()->setLoginSecondaryCredentials($secondaryCred));

        return $next($request);
    }

    public function terminate($request, $response)
    {
        if ($user = Auth::User()) {
            $user->loged_at = date('Y-m-d H:i:s');
            $user->save();
        }
    }

    private function getApiHash($request)
    {
        if ($hash = $request->get('user_api_hash'))
            return $hash;

        if ($hash = $request->header('user-api-hash'))
            return $hash;

        return null;
    }

}
