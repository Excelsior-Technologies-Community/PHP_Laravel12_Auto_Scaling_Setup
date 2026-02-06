<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scaling_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('current_workers');
            $table->integer('new_workers');
            $table->integer('load_percentage');
            $table->string('action'); // 'scale_up', 'scale_down', 'maintain'
            $table->text('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scaling_logs');
    }
};