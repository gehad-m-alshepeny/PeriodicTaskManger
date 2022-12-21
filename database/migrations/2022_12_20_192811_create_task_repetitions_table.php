<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_repetitions', function (Blueprint $table) {
            $table->id();
            $table->string('repeat_at');
            $table->unsignedBigInteger('task_id')->index("task_id_foreign");
            $table->foreign(['task_id'])->references(['id'])->on('tasks')->onDelete('CASCADE');
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
        Schema::dropIfExists('task_repetitions');
    }
};
