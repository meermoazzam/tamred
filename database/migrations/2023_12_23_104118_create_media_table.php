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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('mediable_id');
            $table->string('mediable_type', 100);
            $table->string('type', 50)->nullable();
            $table->integer('size')->nullable();
            $table->string('media_key')->nullable();
            $table->string('thumbnail_key')->nullable();
            $table->integer('sequence')->default(1);
            $table->string('status', 50)->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
