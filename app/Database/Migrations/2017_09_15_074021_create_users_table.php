<?php
/**
 * Two - CreateUsersTable
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

use Two\Support\Facades\Schema;
use Two\Database\Schema\Blueprint;
use Two\Database\Migrations\Migration;


class CreateUsersTable extends Migration
{
    /**
     * Exécutez les migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table)
        {
            $table->increments('id');

            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('realname')->nullable();
            $table->string('email', 100)->unique();
            $table->string('remember_token')->nullable();
            $table->string('api_token', 100)->unique()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Inversez les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
