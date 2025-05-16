<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('postal_code')->after('phone');
            $table->string('address')->after('postal_code');
            $table->string('building')->nullable()->after('address');
        });
    }

    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'address', 'building']);
        });
    }
}
