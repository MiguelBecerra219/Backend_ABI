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
        Schema::create('content_framework_project', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_framework_id')
                ->constrained('content_frameworks')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('project_id')
                ->constrained('projects')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_framework_project');
    }
};
