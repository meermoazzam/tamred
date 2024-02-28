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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status', 50);
            $table->string('title', 1000)->nullable();
            $table->string('description', 10000)->nullable();
            $table->bigInteger('total_likes')->default(0);
            $table->bigInteger('total_comments')->default(0);
            $table->string('location');
            $table->string('latitude', 20);
            $table->string('longitude', 20);
            $table->string('city', 200)->nullable();
            $table->string('state', 200)->nullable();
            $table->string('country', 200)->nullable();
            $table->json('tags')->nullable();
            $table->json('tagged_users')->nullable();
            $table->boolean('allow_comments')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
