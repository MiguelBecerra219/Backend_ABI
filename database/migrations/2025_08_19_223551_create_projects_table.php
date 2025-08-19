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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('title', 255);
            $table->text('evaluation_criteria')
                ->nullable()
                ->default(null);
            $table->foreignId('thematic_area_id')
                ->constrained('thematic_areas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('project_status_id')
                ->constrained('project_statuses')
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
        Schema::dropIfExists('projects');
    }
};
