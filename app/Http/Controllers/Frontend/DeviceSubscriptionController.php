<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\DeviceModalHelper;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Validators\DeviceConfiguratorFormValidator;
use Illuminate\Http\Request;
use Tobuli\Entities\ApnConfig;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceConfig;
use Tobuli\Entities\Subscription;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\SMSGatewayManager;
use Tobuli\Services\DeviceConfigService;
use Auth;

class DeviceSubscriptionController extends Controller
{
    public function index()
    {
        $this->checkException('devices', 'view');

        return view('front::DeviceSubscription.index', [
            'devices' => $this->user
                ->devices()
                ->hasExpiration()
                ->with('subscriptions')
                ->paginate()
        ]);
    }

    public function table()
    {
        $this->checkException('devices', 'view');

        return view('front::DeviceSubscription.table', [
            'devices' => $this->user->devices()->hasExpiration()->with('subscriptions')->paginate()
        ]);
    }

    public function doDestroy($id)
    {
        $subscription = Subscription::find($id);

        $this->checkException('subscriptions', 'remove', $subscription);

        return view('front::DeviceSubscription.destroy')->with([
            'subscription' => $subscription
        ]);
    }

    public function destroy(Request $request)
    {
        $subscription = Subscription::find($request->id);

        $this->checkException('subscriptions', 'remove', $subscription);

        $subscription->cancel();

        return response()->json([
            'status' => 1
        ]);
    }
}
