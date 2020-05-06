<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propositions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('client_name', 255);
            $table->char('input_id', 100)->unique();
            $table->bigInteger('authorizer_user_id')->unsigned()->index();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->timestamps();
            $table->softDeletes();
            
            //外部キー制約
            $table->foreign('authorizer_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposltions');
    }
}
