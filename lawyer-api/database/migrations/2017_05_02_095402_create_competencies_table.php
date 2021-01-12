<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competencies', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->enum('status', ['ENABLED', 'DISABLED'])->default('ENABLED');
            $table->timestamps();
        });

        Schema::create('competency_translations', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('competency_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('name');
            $table->timestamps();

            $table->foreign('language_id', 'competency_translations_language_id_foreign')
                ->references('id')->on('languages')
                ->onDelete('cascade');

            $table->foreign('competency_id', 'competency_translations_competency_id_foreign')
                ->references('id')->on('competencies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('competency_translations', function (Blueprint $table) {
            $table->dropForeign('competency_translations_language_id_foreign');
            $table->dropForeign('competency_translations_competency_id_foreign');
        });

        Schema::drop('competencies');
        Schema::drop('competency_translations');
    }
}
