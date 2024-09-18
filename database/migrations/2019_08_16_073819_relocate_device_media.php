<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

class RelocateDeviceMedia extends Migration
{
    private $oldPath;
    private $newPath;

    public function __construct()
    {
        $this->oldPath = public_path('images/requestPhoto');
        $this->newPath = cameras_media_path();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!is_dir($this->oldPath)) {
            return;
        }

        if (!is_dir($this->newPath)) {
            mkdir($this->newPath);
        }

        //@TODO: discuss- remove apache's permissions(exec/write) for newDir?

        File::move($this->oldPath, $this->newPath);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!is_dir($this->newPath)) {
            return;
        }

        if (!is_dir($this->oldPath)) {
            mkdir($this->oldPath);
        }

        File::move($this->newPath, $this->oldPath);
    }
}
