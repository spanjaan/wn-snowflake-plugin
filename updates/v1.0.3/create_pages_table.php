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
        Schema::create('spanjaan_snowflake_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('meta_keywords', 512)->nullable();
            $table->string('meta_desc', 512)->nullable();
            $table->string('filename',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spanjaan_snowflake_pages');
    }
};
