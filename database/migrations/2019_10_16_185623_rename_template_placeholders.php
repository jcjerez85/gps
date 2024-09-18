<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\SmsTemplate;

class RenameTemplatePlaceholders extends Migration {

    protected $map = [
        '[device]'      => '[device.name]',
        '[odometer]'    => '[device.odometer]',
        '[service]'     => '[service.name]',
        '[left]'        => '[service.left]',
        '[expires]'     => '[service.expires]',
        '[description]' => '[service.description]',
    ];

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        foreach ($this->map as $search => $replace)
        {
            EmailTemplate::whereNotNull('name')->update([
                'title' => DB::raw("REPLACE(title, '$search', '$replace')"),
                'note'  => DB::raw("REPLACE(note, '$search', '$replace')"),
            ]);

            SmsTemplate::whereNotNull('name')->update([
                'title' => DB::raw("REPLACE(title, '$search', '$replace')"),
                'note'  => DB::raw("REPLACE(note, '$search', '$replace')"),
            ]);
        }
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        foreach ($this->map as $replace => $search)
        {
            EmailTemplate::whereNotNull('name')->update([
                'title' => DB::raw("REPLACE(title, '$search', '$replace')"),
                'note'  => DB::raw("REPLACE(note, '$search', '$replace')"),
            ]);

            SmsTemplate::whereNotNull('name')->update([
                'title' => DB::raw("REPLACE(title, '$search', '$replace')"),
                'note'  => DB::raw("REPLACE(note, '$search', '$replace')"),
            ]);
        }
	}

}
