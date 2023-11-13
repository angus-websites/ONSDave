<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->references('id')
                ->on('employees')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('leave_type_id')
                ->references('id')
                ->on('leave_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_records');
    }
};
