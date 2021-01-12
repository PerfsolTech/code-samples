<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLawyerProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('lawyer_profiles', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('bar_number')->nullable(true);
            $table->date('birthday')->nullable(true);
            $table->date('practicing_date')->nullable(true);
            $table->string('address')->nullable(true);
            $table->string('website')->nullable(true);
            $table->string('firm')->nullable(true);
            $table->string('linkedin')->nullable(true);
            $table->double('rating', false, true)->default(0)->unsigned();
            $table->integer('cases')->default(0)->unsigned();
            $table->string('about', 1000)->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'lawyers_user_id_foreign')
                ->references('id')->on('users')
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

        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->dropForeign('lawyers_user_id_foreign');
        });
        Schema::drop('lawyer_profiles');

    }
}
