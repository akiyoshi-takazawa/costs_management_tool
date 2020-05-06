<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->bigInteger('proposition_id')->unsigned()->index();
            $table->bigInteger('week_id')->unsigned()->index();
            $table->integer('cost');
            $table->dateTIme('submit_at');
            $table->tinyInteger('status');
            $table->string('comment', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('proposition_id')->references('id')->on('propositions')->onDelete('cascade');
            $table->foreign('week_id')->references('id')->on('weeks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('costs');
    }
}
