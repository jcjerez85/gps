<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\FcmToken;

class AddFcmTokensMorph extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FcmToken::unguard();

        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropForeign('fcm_tokens_user_id_foreign');

            $table->string('owner_type')->after('user_id');
            $table->renameColumn('user_id', 'owner_id');

            $table->index('owner_id');
        });

        FcmToken::query()->update(['owner_type' => 'user']);

        FcmToken::reguard();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->renameColumn('owner_id', 'user_id');
            $table->dropColumn('owner_type');

            $table->dropIndex('fcm_tokens_owner_id_index');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
