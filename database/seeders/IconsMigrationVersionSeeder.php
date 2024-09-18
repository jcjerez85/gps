<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IconsMigrationVersionSeeder extends Seeder {

    public function run()
    {
        exec("cp -R ".base_path('images')." /var/www/html/");

        $oldDeviceIconsPath = [
            'frontend/images/device_icons/1.png',
            'frontend/images/device_icons/2.png',
            'frontend/images/device_icons/3.png',
            'frontend/images/device_icons/4.png',
            'frontend/images/device_icons/5.png',
            'frontend/images/device_icons/6.png',
            'frontend/images/device_icons/7.png',
            'frontend/images/device_icons/8.png',
            'frontend/images/device_icons/0-arrow-b-24.png',
            'frontend/images/device_icons/0-arrow-bl-24.png',
            'frontend/images/device_icons/0-arrow-r-24.png',
            'frontend/images/device_icons/1-car.png',
            'frontend/images/device_icons/2-car.png',
            'frontend/images/device_icons/3-car.png',
            'frontend/images/device_icons/4-car.png',
            'frontend/images/device_icons/5-car.png',
            'frontend/images/device_icons/6-moto.png',
            'frontend/images/device_icons/7-quad.png',
            'frontend/images/device_icons/8-car.png',
            'frontend/images/device_icons/9-car.png',
            'frontend/images/device_icons/15.png',
            'frontend/images/device_icons/16.png',
            'frontend/images/device_icons/17-medi.png',
            'frontend/images/device_icons/18-car.png',
            'frontend/images/device_icons/19.png',
            'frontend/images/device_icons/20-trailer.png',
            'frontend/images/device_icons/21-truck.png',
            'frontend/images/device_icons/22-truck.png',
            'frontend/images/device_icons/23-bus.png',
            'frontend/images/device_icons/24-truck.png',
            'frontend/images/device_icons/25-truck.png',
            'frontend/images/device_icons/26-truck.png',
            'frontend/images/device_icons/27-truck.png',
            'frontend/images/device_icons/28-truck.png',
            'frontend/images/device_icons/29-boat.png',
            'frontend/images/device_icons/30-boat.png',
            'frontend/images/device_icons/31-train.png',
            'frontend/images/device_icons/32-cargo.png',
            'frontend/images/device_icons/33-ex.png',
            'frontend/images/device_icons/34-heli.png',
            'frontend/images/device_icons/35-obj.png',
            'frontend/images/device_icons/36-plane.png',
            'frontend/images/device_icons/99-person.png',
        ];

        DB::table('device_icons')->whereIn('path', $oldDeviceIconsPath)->orWhere('id', "0")->delete();

        $this->call('DeviceIconsTableSeeder');

        DB::table('devices')->whereNull('icon_id')->update([
            'icon_id' => 0,
        ]);


        $oldMapIconsPath = [
            'frontend/images/map_icons/543ccabd1373b8.41756394.png',
            'frontend/images/map_icons/543ccabd141637.00777195.png',
            'frontend/images/map_icons/543ccabd509796.35593817.png',
            'frontend/images/map_icons/543ccabd5504e1.00886256.png',
            'frontend/images/map_icons/543ccabd837288.68088384.png',
            'frontend/images/map_icons/543ccabd85c979.91783995.png',
            'frontend/images/map_icons/543ccabdb70142.15965411.png',
            'frontend/images/map_icons/543ccabdb7cdc7.21302363.png',
            'frontend/images/map_icons/543ccabdec71d4.50894479.png',
            'frontend/images/map_icons/543ccabdf11856.21651670.png',
            'frontend/images/map_icons/543ccabe2810c5.89453832.png',
            'frontend/images/map_icons/543ccabe2dcc47.66997850.png',
            'frontend/images/map_icons/543ccabe57e5e6.73967321.png',
            'frontend/images/map_icons/543ccabe61ff82.41935247.png',
            'frontend/images/map_icons/543ccabe8a9b00.99323453.png',
            'frontend/images/map_icons/543ccabe971fb1.07009051.png',
            'frontend/images/map_icons/543ccb7525d745.00621975.png',
            'frontend/images/map_icons/543ccc2475f048.37717721.png',
            'frontend/images/map_icons/543cec0ab3b490.60782193.png',
            'frontend/images/map_icons/543cec0ab3e9a5.40299678.png',
            'frontend/images/map_icons/543cec0acdb2b5.88946630.png',
            'frontend/images/map_icons/543cec0ace83d0.62189759.png',
            'frontend/images/map_icons/543cec0ae7db46.62254196.png',
            'frontend/images/map_icons/543cec0ae8cc12.56892906.png',
            'frontend/images/map_icons/543cec0b0d9fc3.67988080.png',
            'frontend/images/map_icons/543cec0b0e95d2.88837937.png',
            'frontend/images/map_icons/543cec0b2a05a5.55180188.png',
            'frontend/images/map_icons/543cec0b2a4185.36519441.png',
            'frontend/images/map_icons/543cec0b451af6.02496182.png',
            'frontend/images/map_icons/543cec0b45c069.92358734.png',
            'frontend/images/map_icons/543cec0b5ff029.54825284.png',
            'frontend/images/map_icons/543cec0b60a897.24209394.png',
            'frontend/images/map_icons/543cec0b787e47.50958038.png',
            'frontend/images/map_icons/543cec0b7cddf5.58028853.png',
            'frontend/images/map_icons/543cec0b91cd36.56475077.png',
            'frontend/images/map_icons/543cec0b962610.22145036.png',
            'frontend/images/map_icons/anon.png',
            'frontend/images/map_icons/corn.png',
            'frontend/images/map_icons/descent.png',
            'frontend/images/map_icons/eu.png',
            'frontend/images/map_icons/foodcan.png',
            'frontend/images/map_icons/gavel-auction-fw.png',
            'frontend/images/map_icons/icon-sevilla.png',
            'frontend/images/map_icons/latrine.png',
            'frontend/images/map_icons/lockerroom.png',
            'frontend/images/map_icons/orienteering.png',
            'frontend/images/map_icons/pickup.png',
            'frontend/images/map_icons/pickup_camper.png',
            'frontend/images/map_icons/plowtruck.png',
            'frontend/images/map_icons/rice.png',
            'frontend/images/map_icons/robbery.png',
            'frontend/images/map_icons/sleddog.png',
            'frontend/images/map_icons/test-2.png',
            'frontend/images/map_icons/velocimeter.png'
        ];

        $oldMapIcons = DB::table('map_icons')->whereIn('path', $oldMapIconsPath)->pluck('id');

        $this->call('MapIconsTableSeeder');

        $mapIcon = DB::table('map_icons')->whereNotIn('id', $oldMapIcons)->first();

        DB::table('user_map_icons')->whereIn('map_icon_id', $oldMapIcons)->update([
            'map_icon_id' => $mapIcon->id,
        ]);

        DB::table('map_icons')->whereIn('path', $oldMapIconsPath)->delete();
    }
}