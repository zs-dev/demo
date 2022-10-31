<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('path', 200);
            $table->string('title', 200)->nullable();
            $table->smallInteger('status');
            $table->unsignedBigInteger('crawler_request_id');
            $table->decimal('load_time', $precision = 7, $scale = 6);

            $table
                ->foreign('crawler_request_id')
                ->references('id')
                ->on('crawler_requests')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
        Schema::dropIfExists('pages');
    }
}
