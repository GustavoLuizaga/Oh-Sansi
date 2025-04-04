<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Iu extends Migration
{
    public function up(){
        Schema::create('iu',function(Blueprint $table){
            $table->id('idIu');
            $table->string('nombreIu');

        });
    }


    public function down(){
        Schema::drop('iu');
    }
}
