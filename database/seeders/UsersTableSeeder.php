<?php

namespace Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Tobuli\Services\PermissionService;

class UsersTableSeeder extends Seeder {

    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $password = 'adminAdmin7*';

        DB::table('users')->insert([
            'email' => 'admin@server.com',
            'email_verified_at' => $now,
            'password' => Hash::make($password),
            'group_id' => 1,
            'map_id' => config('tobuli.main_settings.default_map'),
            'available_maps' => serialize(config('tobuli.main_settings.available_maps')),
            'ungrouped_open' => json_encode(['geofence_group' => 1, 'device_group' => 1, 'poi_group' => 1]),
        ]);
        
        $permissions = (new PermissionService())->getByGroupId(PermissionService::GROUP_ADMIN);

        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $user_permissions = [];

            foreach ($permissions as $name => $modes)
            {
                $user_permissions[] = array_merge([
                    'user_id' => $user->id,
                    'name' => $name,
                ], $modes);
            }

            DB::table('user_permissions')->insert($user_permissions);
        }

    }
}