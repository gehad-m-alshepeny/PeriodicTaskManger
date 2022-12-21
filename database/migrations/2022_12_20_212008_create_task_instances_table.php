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
        Schema::create('task_instances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')->index("task_id_foreign");
            $table->enum('status', ['Pending', 'Completed', 'notDone'])->default('Pending');
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
        Schema::dropIfExists('task_instances');
    }
};
