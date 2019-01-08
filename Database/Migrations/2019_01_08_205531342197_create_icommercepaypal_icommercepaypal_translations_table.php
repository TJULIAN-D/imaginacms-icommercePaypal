<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcommercepaypalIcommercePaypalTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icommercepaypal__icommercepaypal_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            // Your translatable fields

            $table->integer('icommercepaypal_id')->unsigned();
            $table->string('locale')->index();
            $table->unique(['icommercepaypal_id', 'locale']);
            $table->foreign('icommercepaypal_id')->references('id')->on('icommercepaypal__icommercepaypals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('icommercepaypal__icommercepaypal_translations', function (Blueprint $table) {
            $table->dropForeign(['icommercepaypal_id']);
        });
        Schema::dropIfExists('icommercepaypal__icommercepaypal_translations');
    }
}
