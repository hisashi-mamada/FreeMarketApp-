<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProfilesMakePostalNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('postal_code')->nullable()->change();
            $table->string('address')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('postal_code')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
        });
    }
}
