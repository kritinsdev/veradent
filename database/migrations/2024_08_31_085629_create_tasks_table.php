<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id');
            $table->string('patient_name');
            $table->string('color');
            $table->integer('scan_models')->default(0);
            $table->integer('3d_models')->default(0);
            $table->boolean('3d_models_full')->default(0);
            $table->decimal('total_price')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
