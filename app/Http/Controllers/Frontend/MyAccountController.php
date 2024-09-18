<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use ModalHelpers\MyAccountSettingsModalHelper;
use Tobuli\Repositories\User\UserRepositoryInterface as User;
use Tobuli\Validation\UserAccountFormValidator;

class MyAccountController extends Controller {

    public function edit(User $userRepo) {
        $item = $userRepo->find(Auth::User()->id);

        return view('front::MyAccount.edit')->with(compact('item'));
    }

    public function update(MyAccountSettingsModalHelper $myAccountSettingsModalHelper, User $userRepo, UserAccountFormValidator $userAccountFormValidator) {
        $input = Request::all();
        $data = $myAccountSettingsModalHelper->changePassword($input, Auth::User(), $userRepo, $userAccountFormValidator);

        return response()->json($data);
    }

    public function changeMap(User $userRepo)
    {
        if ( isDemoUser() )
            return response()->json(['status' => 1, 'demo' => true]);

        $input = Request::all();
        $selected = trim($input['selected']);
        $maps     = Config::get('tobuli.maps');
        $map_id   = settings('main_settings.default_map');

        if (isset($maps[$selected]))
            $map_id = $maps[$selected];

        $userRepo->update(Auth::User()->id, [
            'map_id' => $map_id
        ]);

        return response()->json(['status' => 1, 'map_id' => $map_id]);
    }
}