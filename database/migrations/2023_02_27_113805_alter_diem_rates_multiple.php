<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\DiemRate;

class AlterDiemRatesMultiple extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('diem_rates', 'rates')) {
            Schema::table('diem_rates', function (Blueprint $table) {
                $table->text('rates');
            });

            /** @var DiemRate $diemRate */
            foreach (DiemRate::cursor() as $i => $diemRate) {
                $diemRate->rates = [['amount' => $diemRate->amount, 'period' => $diemRate->period]];
                $diemRate->save();
            }
        }

        if (Schema::hasColumn('diem_rates', 'amount')) {
            Schema::table('diem_rates', function (Blueprint $table) {
                $table->dropColumn('amount');
                $table->dropColumn('period');
                $table->dropColumn('period_unit');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('diem_rates', 'period_unit')) {
            Schema::table('diem_rates', function (Blueprint $table) {
                $table->float('amount');
                $table->unsignedSmallInteger('period');
                $table->string('period_unit', 1);
            });

            /** @var DiemRate $diemRate */
            foreach (DiemRate::cursor() as $i => $diemRate) {
                $rates = $diemRate->rates[0] ?? null;

                if (!empty($rates)) {
                    $diemRate->amount = $rates['amount'] ?? 0;
                    $diemRate->period = $rates['period'] ?? 0;
                }

                $diemRate->period_unit = 'h';
                $diemRate->save();
            }
        }

        if (Schema::hasColumn('diem_rates', 'rates')) {
            Schema::table('diem_rates', function (Blueprint $table) {
                $table->dropColumn('rates');
            });
        }
    }
}
