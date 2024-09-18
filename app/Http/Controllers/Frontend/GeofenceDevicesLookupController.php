<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\LookupController;
use Illuminate\Http\Request;
use Tobuli\Entities\Geofence;
use Tobuli\Lookups\Tables\DevicesGeofenceLookupTable;

/**
 * @property DevicesGeofenceLookupTable $lookup
 */
class GeofenceDevicesLookupController extends LookupController
{
    public function __construct(Request $request)
    {
        parent::__construct($request, 'devices_geofence');
    }

    public function index($id = null)
    {
        $this->setGeofence($id);

        return parent::index();
    }

    public function table($id = null)
    {
        $this->setGeofence($id);

        return parent::table();
    }

    public function data($id = null)
    {
        $this->setGeofence($id);

        return parent::data();
    }

    private function setGeofence($id)
    {
        $geofence = Geofence::find($id);

        $this->checkException('geofences', 'show', $geofence);

        $this->lookup->setGeofenceId($id);
    }
}
