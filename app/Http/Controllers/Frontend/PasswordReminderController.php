<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\Repositories\UserRepo;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class PasswordReminderController extends Controller {

    /*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

    use ResetsPasswords;

    /**
     * The password broker implementation.
     *
     * @var PasswordBroker
     */
    protected $passwords;

    function __construct(PasswordBroker $passwords)
    {
        $this->passwords = $passwords;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return View::make('front::PasswordReminder.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        try {
            $response = $this->passwords->sendResetLink(Request::only('email'));

            return Redirect::route('password_reminder.create')->withInput()->with(($response == PasswordBroker::RESET_LINK_SENT ? 'success' : 'message'),
                trans($response));
        } catch (\Exception $e) {
            return Redirect::route('password_reminder.create')->withInput()->with('message', 'Failed to send mail. Check email settings.');
        }
    }

    public function reset($token)
    {
        return View::make('front::PasswordReminder.reset')->with('token', $token);
    }

    public function update()
    {
        $input = Request::only(['email', 'password', 'password_confirmation', 'token']);

        $res = Password::reset($input, function($user, $password)
        {
            UserRepo::update($user->id, ['password' => $password]);
        });

        if ($res == Password::PASSWORD_RESET)
            return Redirect::route('login')->with('success', trans($res));
        else
            return Redirect::route('password.reset', $input['token'])->with('message', trans($res))->withInput();
    }
} 