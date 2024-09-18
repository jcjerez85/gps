<?php namespace App\Http\Controllers;

use App\Events\UserFirstLoginEvent;
use App\Exceptions\Manager;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\User;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data;
    protected $api;

    /** @var User */
    protected $user;

    protected $exceptionManager;

    /**
     * @return array
     */
    protected static function unexpectedErrorResponse()
    {
        return ['status' => 0, 'errors' => ['id' => trans('front.unexpected_error')]];
    }

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->exceptionManager = new Manager($this->user);
            $this->api =  boolval(Config::get('tobuli.api') == 1);
            $this->data = request()->all();

            $this->setActingUser();
            $this->afterAuth($this->user);

            return $next($request);
        });
    }

    protected function afterAuth($user){}

    public function checkException($repo, $action, $model = null)
    {
        $this->exceptionManager->check($repo, $action, $model);
    }

    private function setActingUser()
    {
        if ( ! $this->user)
            return;

        if ( ! $this->user->isLoggedBefore()) {
            event(new UserFirstLoginEvent($this->user));
        }

        setActingUser($this->user);
    }
}
