<?php

use Two\Database\Schema\Blueprint;
use Two\Database\Migrations\Migration;


class CreateTwoAuthorsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function (Blueprint $table)
        {
            $table->string('aid',30)->default('');
            $table->string('name',50)->nullable()->default(null);
            $table->string('url',320)->nullable()->default(null);
            $table->string('email',254)->nullable()->default(null);
            $table->string('pwd',60)->nullable()->default(null);
            $table->integer('hashkey')->default(0);
            $table->integer('counter')->default(0);
            $table->integer('radminfilem')->default(0);
            $table->integer('radminsuper')->default(1);
            $table->primary('aid');            
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
        Schema::dropIfExists('authors');
    }
}
