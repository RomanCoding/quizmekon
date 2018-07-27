<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('type');
            $table->unsignedInteger('video_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('series_id')->nullable();
            $table->unsignedTinyInteger('order')->nullable();
            $table->unsignedInteger('category_id');
            $table->string('question');
            $table->text('answers');
            $table->unsignedTinyInteger('correct_index');
            $table->integer('score')->default(0);
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
        Schema::dropIfExists('quizzes');
    }
}
