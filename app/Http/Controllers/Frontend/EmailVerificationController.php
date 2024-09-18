<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\User;

class EmailVerificationController extends Controller
{
    public function notice(): \Illuminate\Contracts\View\View
    {
        $url = session()->get('url', route('home'));

        return View::make('verification', [
            'url' => $url,
        ]);
    }

    public function verify(string $token): RedirectResponse
    {
        try {
            list($hash, $id) = explode(';', $token, 2);
        } catch (\Exception $exception) {
            $hash = $id = null;
        }

        $user = User::find($id);

        if (!($user && hash_equals($hash, sha1($user->email)))) {
            return redirect()->route('login');
        }

        $user->markEmailAsVerified();

        if (config('verification.autologin')) {
            Auth::loginUsingId($user->id);
        }

        return redirect()->to(config('verification.redirect'));
    }
}