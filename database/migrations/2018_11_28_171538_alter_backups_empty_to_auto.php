<?php

use Illuminate\Database\Migrations\Migration;

class AlterBackupsEmptyToAuto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ftp_server = settings('backups.ftp_server');

        if ( ! empty($ftp_server)) {
            return;
        }

        settings('backups', [
            'type' => 'auto',
            'ftp_server'   => null,
            'ftp_username' => null,
            'ftp_password' => null,
            'ftp_port'     => null,
            'ftp_path'     => null,
        ]);
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
