<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('files.table_name'), function (Blueprint $table) {
            $table->increments('id');

            $table->tinyInteger('order')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_hash', 8);
            $table->integer('file_size')->nullable();
            $table->string('content_type')->nullable();

            $table->string('attachable_type')->nullable();
            $table->integer('attachable_id')->unsigned()->index()->nullable();

            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('files.table_name'));
    }
}
