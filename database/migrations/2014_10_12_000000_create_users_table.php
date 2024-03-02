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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin')->default(0);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('bio')->nullable();
            $table->string('nickname')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('location');
            $table->decimal('latitude', 20, 10);
            $table->decimal('longitude', 20, 10);
            $table->string('city', 200)->nullable();
            $table->string('state', 200)->nullable();
            $table->string('country', 200)->nullable();
            $table->string('image')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('cover')->nullable();
            $table->string('device_id')->nullable();
            $table->string('notification_settings', 2000)->nullable()->default(json_encode(config('constants.notification_settings')));
            $table->string('status', 50)->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
