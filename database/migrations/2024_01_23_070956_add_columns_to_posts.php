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
        Schema::table('posts', function (Blueprint $table) {
            $table->bigInteger('total_likes')->default(0)->after('description');
            $table->unsignedBigInteger('album_id')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'album_id')) {
                $table->dropColumn('album_id');
            }
            if (Schema::hasColumn('posts', 'total_likes')) {
                $table->dropColumn('total_likes');
            }
        });
    }
};
