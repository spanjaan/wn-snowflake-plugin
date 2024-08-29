<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spanjaan_snowflake_elements', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('page_id')->nullable()->default(null);
            $table->integer('layout_id')->nullable()->default(null);
            $table->integer('partial_id')->nullable()->default(null);
            $table->integer('type_id')->nullable();
            $table->string('desc', 255)->nullable();
            $table->text('content')->nullable();
            $table->boolean('in_use')->default(1);
            $table->string('alt')->nullable();
            $table->string('cms_key')->nullable();
            $table->integer('order')->nullable();
            $table->string('filename')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spanjaan_snowflake_elements');
    }
};
