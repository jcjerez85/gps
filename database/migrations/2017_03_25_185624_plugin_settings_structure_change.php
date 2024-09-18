<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PluginSettingsStructureChange extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $item = DB::table('configs')->where('title', '=', 'plugins')->first();

        if ($item) {
            $plugins = unserialize($item->value);

            foreach ($plugins as $key => $status) {
                $plugins[$key] = [
                    'status' => $status,
                ];
            }

            $value = serialize($plugins);

            DB::table('configs')->where('title', '=', 'plugins')->update(['value' => $value]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
