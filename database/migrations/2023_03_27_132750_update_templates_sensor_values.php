<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\SmsTemplate;

class UpdateTemplatesSensorValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->updateNotes(EmailTemplate::query(), '[device.odometer]', '[device.sensor.odometer]');
        $this->updateNotes(SmsTemplate::query(), '[device.odometer]', '[device.sensor.odometer]');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->updateNotes(EmailTemplate::query(), '[device.sensor.odometer]', '[device.odometer]');
        $this->updateNotes(SmsTemplate::query(), '[device.sensor.odometer]', '[device.odometer]');
    }

    private function updateNotes(Builder $query, string $search, string $replace)
    {
        $templates = $query->where('note', 'LIKE', '%' . $search . '%')->cursor();

        foreach ($templates as $template) {
            $template->note = str_replace($search, $replace, $template->note);
            $template->save();
        }
    }
}
