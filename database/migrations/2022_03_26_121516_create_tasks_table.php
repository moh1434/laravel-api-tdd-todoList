<?php

use App\Models\Task;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('todo_list_id')
                ->constrained()
                ->onDelete('cascade');
            $table->enum('status', [Task::STARTED, Task::PENDING, Task::NOT_STARTED])
                ->default(Task::NOT_STARTED);
            $table->text('description')->nullable();
            $table->string('title');
            $table->foreignId('label_id')->constrained();
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
        Schema::dropIfExists('tasks');
    }
}
