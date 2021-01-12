<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSocial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_socials');
        Schema::create('user_socials', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('identity');
            $table->enum('type', ['facebook']);
            $table->enum('status', ['ACTIVE', 'PENDING'])->default('ACTIVE');
            $table->string('activation_token')->nullable();
            $table->timestamps();


            $table->foreign('user_id', 'user_socials_user_id_foreign')
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
        Schema::table('user_socials', function (Blueprint $table) {
            $table->dropForeign('user_socials_user_id_foreign');
        });

        Schema::dropIfExists('user_socials');
    }
}
