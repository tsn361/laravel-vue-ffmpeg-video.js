<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->boolean('stg_autoplay')->default(false);
            $table->boolean('stg_muted')->default(false);
            $table->boolean('stg_loop')->default(false);
            $table->boolean('stg_autopause')->default(false);
            $table->string('stg_preload_configration')->default('none');
        });
    }
};