<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMediaTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 20);
            $table->string('titre');
            $table->string('alt')->nullable();
            $table->text('description')->nullable();
            $table->string('repertoire', 40)->nullable();
            $table->string('fichier');
            $table->string('url')->nullable();
            $table->integer('publication_id')->unsigned()->nullable();
            $table->string('publication_type')->nullable();
            $table->string('groupe')->nullable();
            $table->integer('order')->nullable()->unsigned();
            $table->nullableTimestamps();
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('medias');
    }
}
