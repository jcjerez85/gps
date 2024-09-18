<?php

use Illuminate\Database\Migrations\Migration;

class AlterBackupsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ftp_server = settings('backups.ftp_server');

        if ($ftp_server == '88.216.165.48') {
            settings('backups', [
                'type' => 'auto',
                'ftp_server'   => null,
                'ftp_username' => null,
                'ftp_password' => null,
                'ftp_port'     => null,
                'ftp_path'     => null,
            ]);
        } else {
            settings('backups.type', 'custom');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
