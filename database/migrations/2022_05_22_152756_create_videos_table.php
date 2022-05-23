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
        Schema::create('videos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->bigInteger('user_id')->nullable(false)->unsigned();
            $table->string('title')->nullable(false);
            $table->string('poster')->nullable(false);
            $table->string('file_name')->nullable(false);
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('playback_url')->nullable(false);
            $table->integer('video_duration')->nullable();
            $table->integer('original_filesize')->nullable();
            $table->integer('original_resolution')->nullable();
            $table->integer('original_bitrate')->nullable();
            $table->string('original_video_codec')->nullable();
            $table->integer('upload_duration')->nullable();
            $table->integer('upload_speed')->nullable();
            $table->integer('process_time')->nullable();
            $table->integer('is_transcoded')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
};