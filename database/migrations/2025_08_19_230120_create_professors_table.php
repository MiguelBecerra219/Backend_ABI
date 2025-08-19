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
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->string('card_id', 25)->unique();
            $table->string('name', 50);
            $table->string('last_name', 50);
            $table->string('mail', 50);
            $table->string('phone', 20);
            $table->boolean('state');
            $table->boolean('committee_leader');
            $table->foreignId('city_program_id')
                ->constrained('city_program')
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
        Schema::dropIfExists('professors');
    }
};
