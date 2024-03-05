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
        Schema::create('adds', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500)->nullable();
            $table->string('author')->nullable();
            $table->string('link', 1000)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('gender');
            $table->string('min_age');
            $table->string('max_age');
            $table->decimal('latitude', 20, 10);
            $table->decimal('longitude', 20, 10);
            $table->integer('range');
            $table->string('status', 50)->default('created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adds');
    }
};
