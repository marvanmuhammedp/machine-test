<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_remarks', function (Blueprint $table) {
           
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->text('remark');
            $table->enum('status', ['Pending', 'In Progress', 'Completed']);
            $table->timestamp('created_at')->useCurrent();
    
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_remarks');
    }
}
